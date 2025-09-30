@echo off
echo Installing dependencies...
cd /d "C:\Users\fredj\Local Sites\mcculloug-digital-2\app\public\wp-content\themes\mccullough-digital"
call npm install
echo.
echo Building theme...
call npm run build
echo.
echo Done!
pause
