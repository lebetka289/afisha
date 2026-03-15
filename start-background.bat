@echo off
chcp 65001 >nul
cd /d "%~dp0"
echo Запуск в фоне...
docker compose up -d --build
if %errorlevel% neq 0 (
  echo Ошибка запуска.
  pause
  exit /b 1
)
echo.
echo Сайт запущен: http://localhost:8000
echo Остановить: запустите stop.bat или выполните docker compose down
echo.
start http://localhost:8000
timeout /t 5
