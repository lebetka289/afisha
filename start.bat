@echo off
chcp 65001 >nul
title Афиша — Docker
echo.
echo  Запуск сайта (Docker)...
echo  После запуска откройте: http://localhost:8000
echo  Остановка: закройте это окно или нажмите Ctrl+C
echo.
cd /d "%~dp0"
docker compose up --build
pause
