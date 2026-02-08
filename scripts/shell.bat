@echo off
cd /d "%~dp0\.."

echo Entering VendingMachine container...
docker exec -it VendingMachine bash
