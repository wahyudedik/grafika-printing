#!/bin/bash

echo "Starting Grafika Printing Development Environment..."
echo

echo "[1/3] Starting Laravel Server..."
php artisan serve &
LARAVEL_PID=$!
sleep 3

echo "[2/3] Starting ngrok tunnel..."
ngrok http 8000 &
NGROK_PID=$!
sleep 5

echo "[3/3] Opening ngrok dashboard..."
if command -v open &> /dev/null; then
    open http://localhost:4040
elif command -v xdg-open &> /dev/null; then
    xdg-open http://localhost:4040
fi

echo
echo "✅ Development environment started!"
echo
echo "📝 Next steps:"
echo "1. Copy ngrok URL from the ngrok window"
echo "2. Update webhook URLs in Xendit Dashboard"
echo "3. Update XENDIT_CALLBACK_URL in .env file"
echo
echo "🔗 Useful URLs:"
echo "- Laravel: http://localhost:8000"
echo "- ngrok Dashboard: http://localhost:4040"
echo "- Xendit Dashboard: https://dashboard.xendit.co/settings/developers#webhooks"
echo
echo "Press Ctrl+C to stop all services"

# Function to cleanup on exit
cleanup() {
    echo "Stopping services..."
    kill $LARAVEL_PID 2>/dev/null
    kill $NGROK_PID 2>/dev/null
    exit
}

# Trap Ctrl+C
trap cleanup SIGINT

# Wait for user to stop
wait
