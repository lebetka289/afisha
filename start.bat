@echo off
chcp 65001 >nul
cd /d "%~dp0"
echo.
echo  OnTheRise — Запуск через Docker
echo  ================================
echo.
echo  Скрипт настроит зеркала Docker Hub, скачает образ и запустит проект.
echo.
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0start.ps1"
pause
