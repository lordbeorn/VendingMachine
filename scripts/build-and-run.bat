@echo off
cd /d "%~dp0\.."

echo Building and starting Docker containers...
docker compose up --build -d
echo Done.
pause
