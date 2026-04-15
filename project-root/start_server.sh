#!/bin/bash

echo "=== EvenTrix Server Startup ==="

# Check if MariaDB is running
if ! pgrep -x mysqld > /dev/null && ! pgrep -x mariadbd > /dev/null; then
    echo "Starting MariaDB..."
    sudo service mariadb start
fi

# Kill existing PHP server
pkill -9 -f "php -S"
sleep 1

# Start PHP server
echo "Starting PHP Development Server on port 8090..."
cd /app/project-root/public && php -S 0.0.0.0:8090 > /tmp/php_server.log 2>&1 &
PHP_PID=$!

sleep 2

# Check if server started
if ps -p $PHP_PID > /dev/null; then
    echo "✓ PHP Server started successfully (PID: $PHP_PID)"
    echo "✓ Landing Page: http://localhost:8090/"
    echo "✓ Login: http://localhost:8090/login.html"
    echo "✓ Dashboard: http://localhost:8090/pages/dashboard.html"
    echo ""
    echo "Admin Credentials:"
    echo "  Email: admin@eventrix.com"
    echo "  Password: password"
else
    echo "✗ Failed to start PHP server"
    exit 1
fi
