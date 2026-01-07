#!/bin/bash
# Start PHP built-in server for local development

echo "Starting PHP development server..."
echo "Server will be available at: http://localhost:8000"
echo ""
echo "Press Ctrl+C to stop the server"
echo ""

cd "$(dirname "$0")"
php -S localhost:8000

