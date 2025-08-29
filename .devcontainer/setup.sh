#!/bin/bash

echo "🚀 Setting up OGSpy development environment..."

# Wait for database to be ready with smarter timing
echo "⏳ Waiting for database to be ready..."

# First, wait a bit for MariaDB to start completely
echo "Giving MariaDB time to initialize (30 seconds)..."
sleep 30

# Then test connection with fewer attempts and longer intervals
MAX_ATTEMPTS=10
ATTEMPT=0
SLEEP_TIME=5

while [ $ATTEMPT -lt $MAX_ATTEMPTS ]; do
    echo "Testing database connection (attempt $((ATTEMPT + 1))/$MAX_ATTEMPTS)..."

    if mysqladmin ping -h db -u root -pogspy_root --silent 2>/dev/null; then
        echo "✅ Database ping successful!"

        # Test actual SQL connection
        if mysql -h db -u root -pogspy_root -e "SELECT 1;" >/dev/null 2>&1; then
            echo "✅ Database is ready for connections!"
            break
        else
            echo "Database responds to ping but not ready for SQL queries yet..."
        fi
    else
        echo "Database not responding to ping yet..."
    fi

    ATTEMPT=$((ATTEMPT + 1))
    if [ $ATTEMPT -lt $MAX_ATTEMPTS ]; then
        echo "Waiting $SLEEP_TIME seconds before next attempt..."
        sleep $SLEEP_TIME
    fi
done

if [ $ATTEMPT -eq $MAX_ATTEMPTS ]; then
    echo "❌ Database connection timeout after $MAX_ATTEMPTS attempts"
    echo "Checking docker container status..."
    docker ps --filter "name=db"
    exit 1
fi

# Test database connection and show database info
echo "🔍 Testing database connection and setup..."
mysql -h db -u root -pogspy_root -e "
SHOW DATABASES;
SELECT 'Database connection successful!' as status;
SELECT @@version as mysql_version;
" || {
    echo "❌ Database connection failed"
    exit 1
}

# Verify OGSpy database and user exist
echo "🔍 Verifying OGSpy database setup..."
mysql -h db -u root -pogspy_root -e "
USE ogspy;
SELECT 'OGSpy database ready!' as status;
SHOW TABLES;
SELECT User, Host FROM mysql.user WHERE User='ogspy_user';
" 2>/dev/null || {
    echo "⚠️  OGSpy database or user not found, but will be created during installation"
}

# Install Composer dependencies
if [ -f "composer.json" ]; then
    echo "📦 Installing PHP dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Install Node.js dependencies
if [ -f "package.json" ]; then
    echo "📦 Installing Node.js dependencies..."
    npm install
fi

# Set up proper permissions
echo "🔐 Setting up permissions..."
sudo chown -R vscode:vscode /workspace
chmod -R 755 /workspace

# Create necessary directories if they don't exist
mkdir -p /workspace/cache
mkdir -p /workspace/logs
chmod -R 777 /workspace/cache
chmod -R 777 /workspace/logs

echo "🎉 Setup complete! You can now:"
echo "  - Access OGSpy at: http://localhost:8080"
echo "  - Access MailHog at: http://localhost:8025"
echo "  - Connect to MariaDB at: localhost:3306"
echo ""
echo "📋 Database credentials for OGSpy installation:"
echo "  Host: db (or localhost from your host machine)"
echo "  Database: ogspy"
echo "  Username: ogspy_user"
echo "  Password: ogspy_password"
echo ""
echo "📋 Root database access (for dev/debug):"
echo "  Username: root"
echo "  Password: ogspy_root"
echo ""
echo "🚀 Next steps:"
echo "  1. Visit http://localhost:8080 to start OGSpy installation"
echo "  2. Use the ogspy_user credentials above during setup"
