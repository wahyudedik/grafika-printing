@echo off
echo Starting Grafika Printing Development Environment...
echo.

echo [1/3] Starting Laravel Server...
start "Laravel Server" cmd /k "php artisan serve"
timeout /t 3 /nobreak >nul

echo [2/3] Starting ngrok tunnel...
start "ngrok Tunnel" cmd /k "ngrok http 8000"
timeout /t 5 /nobreak >nul

echo [3/3] Opening ngrok dashboard...
start http://localhost:4040

echo.
echo ✅ Development environment started!
echo.
echo 📝 Next steps:
echo 1. Copy ngrok URL from the ngrok window
echo 2. Update webhook URLs in Xendit Dashboard
echo 3. Update XENDIT_CALLBACK_URL in .env file
echo.
echo 🔗 Useful URLs:
echo - Laravel: http://localhost:8000
echo - ngrok Dashboard: http://localhost:4040
echo - Xendit Dashboard: https://dashboard.xendit.co/settings/developers#webhooks
echo.
pause
