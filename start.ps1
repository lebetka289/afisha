### OnTheRise — автоматический запуск через Docker
### Решает проблему TLS timeout к Docker Hub (добавляет зеркала)

$ErrorActionPreference = "Continue"
Set-Location $PSScriptRoot

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  OnTheRise - Zapusk cherez Docker" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# --- 1. Check Docker ---
$dockerOk = $false
try { docker info 2>$null | Out-Null; $dockerOk = $true } catch {}

if (-not $dockerOk) {
    Write-Host "[!] Docker ne zapuschen. Zapuskayu Docker Desktop..." -ForegroundColor Yellow
    $dockerExe = "C:\Program Files\Docker\Docker\Docker Desktop.exe"
    if (Test-Path $dockerExe) { Start-Process $dockerExe }
    Write-Host "    Zhdu zapuska Docker (do 120 sekund)..." -ForegroundColor Gray
    $waited = 0
    while ($waited -lt 120) {
        Start-Sleep -Seconds 5
        $waited += 5
        try { docker info 2>$null | Out-Null; $dockerOk = $true; break } catch {}
        Write-Host "    ... $waited sek" -ForegroundColor Gray
    }
    if (-not $dockerOk) {
        Write-Host "[X] Docker ne zapustilsya. Zapustite Docker Desktop vruchnuyu." -ForegroundColor Red
        pause
        exit 1
    }
}
Write-Host "[OK] Docker zapuschen" -ForegroundColor Green

# --- 2. Add Docker Hub mirrors ---
$daemonJsonPath = Join-Path $env:USERPROFILE ".docker\daemon.json"
$mirrors = @("https://mirror.gcr.io","https://dockerhub.timeweb.cloud","https://huecker.io")
$needRestart = $false

try {
    $configObj = $null
    if (Test-Path $daemonJsonPath) {
        $raw = Get-Content $daemonJsonPath -Raw
        if ($raw) { $configObj = $raw | ConvertFrom-Json }
    }
    if ($null -eq $configObj) {
        $parentDir = Split-Path $daemonJsonPath
        if (-not (Test-Path $parentDir)) { New-Item -Path $parentDir -ItemType Directory -Force | Out-Null }
        $configObj = New-Object PSObject
    }
    $existingMirrors = @()
    if ($configObj.PSObject.Properties.Name -contains "registry-mirrors") {
        $existingMirrors = @($configObj.'registry-mirrors')
    }
    $hasMirrors = $true
    foreach ($m in $mirrors) {
        if ($existingMirrors -notcontains $m) { $hasMirrors = $false; break }
    }
    if (-not $hasMirrors) {
        Write-Host "[*] Dobavlyayu zerkala Docker Hub (mirror.gcr.io, timeweb, huecker)..." -ForegroundColor Yellow
        $allMirrors = ($existingMirrors + $mirrors) | Sort-Object -Unique
        if ($configObj.PSObject.Properties.Name -contains "registry-mirrors") {
            $configObj.'registry-mirrors' = $allMirrors
        } else {
            $configObj | Add-Member -NotePropertyName "registry-mirrors" -NotePropertyValue $allMirrors
        }
        $configObj | ConvertTo-Json -Depth 10 | Set-Content $daemonJsonPath -Encoding UTF8
        $needRestart = $true
        Write-Host "    daemon.json obnovlen: $daemonJsonPath" -ForegroundColor Gray
    } else {
        Write-Host "[OK] Zerkala Docker Hub uzhe nastroeny" -ForegroundColor Green
    }
} catch {
    Write-Host "[!] Ne udalos obnovit daemon.json: $_" -ForegroundColor Yellow
}

# --- 3. Restart Docker if needed ---
if ($needRestart) {
    Write-Host "[*] Perezapuskayu Docker Desktop (dlya primeneniya zerkal)..." -ForegroundColor Yellow
    Get-Process "Docker Desktop" -ErrorAction SilentlyContinue | Stop-Process -Force -ErrorAction SilentlyContinue
    Start-Sleep -Seconds 5
    $dockerExe = "C:\Program Files\Docker\Docker\Docker Desktop.exe"
    if (Test-Path $dockerExe) { Start-Process $dockerExe }
    Write-Host "    Zhdu perezapuska Docker (do 90 sekund)..." -ForegroundColor Gray
    $waited = 0
    $dockerOk = $false
    while ($waited -lt 90) {
        Start-Sleep -Seconds 5
        $waited += 5
        try { docker info 2>$null | Out-Null; $dockerOk = $true; break } catch {}
        Write-Host "    ... $waited sek" -ForegroundColor Gray
    }
    if (-not $dockerOk) {
        Write-Host "[X] Docker ne perezapustilsya. Perezapustite Docker Desktop vruchnuyu i povtorite." -ForegroundColor Red
        pause
        exit 1
    }
    Write-Host "[OK] Docker perezapuschen s zerkalami" -ForegroundColor Green
}

# --- 4. Pull base image ---
$imageCached = $false
try { docker image inspect php:8.2-cli 2>$null | Out-Null; $imageCached = $true } catch {}

if (-not $imageCached) {
    Write-Host "[*] Skachvayu bazoviy obraz php:8.2-cli (cherez zerkala)..." -ForegroundColor Yellow
    $pullOk = $false
    for ($attempt = 1; $attempt -le 5; $attempt++) {
        Write-Host "    Popytka $attempt iz 5..." -ForegroundColor Gray
        docker pull php:8.2-cli 2>&1
        if ($LASTEXITCODE -eq 0) { $pullOk = $true; break }
        Write-Host "    Ne udalos, zhdu 10 sekund..." -ForegroundColor Yellow
        Start-Sleep -Seconds 10
    }
    if (-not $pullOk) {
        Write-Host ""
        Write-Host "[X] Ne udalos skachat obraz php:8.2-cli za 5 popytok." -ForegroundColor Red
        Write-Host "    Prichina: Docker Hub nedostupen (TLS timeout)." -ForegroundColor Red
        Write-Host ""
        Write-Host "    Varianty:" -ForegroundColor Yellow
        Write-Host "    1. Vklyuchite VPN i povtorite zapusk" -ForegroundColor White
        Write-Host "    2. Podklyuchites k drugoy seti (mobilniy hotspot)" -ForegroundColor White
        Write-Host "    3. Vypolnite 'docker pull php:8.2-cli' kogda set zarabotaet" -ForegroundColor White
        Write-Host ""
        pause
        exit 1
    }
    Write-Host "[OK] Obraz php:8.2-cli skachan" -ForegroundColor Green
} else {
    Write-Host "[OK] Obraz php:8.2-cli uzhe v keshe" -ForegroundColor Green
}

# --- 5. Build and run ---
Write-Host ""
Write-Host "[*] Sobirayu i zapuskayu OnTheRise..." -ForegroundColor Cyan
Write-Host "    Sayt budet dostupen po adresu: http://localhost:8000" -ForegroundColor Cyan
Write-Host ""
docker compose up --build
