@echo off
chcp 65001 >nul
title Остановка Docker
cd /d "%~dp0"
echo Остановка контейнеров...
docker compose down
echo Готово.
timeout /t 3
