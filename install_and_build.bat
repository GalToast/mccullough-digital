@echo off
echo Installing dependencies...
cd /d "%~dp0"
call npm install
echo.
echo Building theme...
call npm run build
echo.
echo Done!
pause
