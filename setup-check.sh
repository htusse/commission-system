#!/bin/bash

# MLM Commission System - Quick Setup Script
# This script helps you complete the remaining setup for Milestone 1

echo "========================================="
echo "MLM Commission System - Setup Helper"
echo "========================================="
echo ""

# Navigate to project directory
cd "$(dirname "$0")"

echo "📍 Current directory: $(pwd)"
echo ""

# Check if Sail is running
echo "🔍 Checking if Sail containers are running..."
SAIL_STATUS=$(/bin/bash ./vendor/bin/sail ps 2>&1)

if echo "$SAIL_STATUS" | grep -q "mlm-commission-system-laravel.test-1"; then
    echo "✅ Sail is running"
else
    echo "❌ Sail is not running"
    echo "Starting Sail containers..."
    /bin/bash ./vendor/bin/sail up -d
    echo "⏳ Waiting for containers to be ready..."
    sleep 10
fi

echo ""
echo "========================================="
echo "📋 IMPORTANT: Database Import Required"
echo "========================================="
echo ""
echo "Before proceeding to Milestone 2, you must:"
echo ""
echo "1. Place 'nxm_assessment_2023.sql' in:"
echo "   $(pwd)/database/sql/"
echo ""
echo "2. Import the database using the helper script:"
echo ""
echo "   # Easy way (recommended):"
echo "   ./db.sh import"
echo ""
echo "   # Or manually:"
echo "   ./db.sh create-db"
echo "   ./vendor/bin/sail exec -T mariadb mariadb -u sail -ppassword nxm_assessment_2023 < database/sql/nxm_assessment_2023.sql"
echo "   ./vendor/bin/sail exec -T mariadb mariadb -u sail -ppassword nxm_assessment_2023_test < database/sql/nxm_assessment_2023.sql"
echo ""
echo "========================================="
echo "🎯 Quick Commands"
echo "========================================="
echo ""
echo "Access the application:"
echo "  🌐 http://localhost"
echo ""
echo "Database helper commands:"
echo "  ./db.sh                       # Show all commands"
echo "  ./db.sh show-tables           # Show tables"
echo "  ./db.sh console               # Open MariaDB console"
echo "  ./db.sh import                # Import SQL file"
echo ""
echo "Useful Sail commands:"
echo "  ./vendor/bin/sail up -d       # Start containers"
echo "  ./vendor/bin/sail down        # Stop containers"
echo "  ./vendor/bin/sail shell       # Access container shell"
echo "  ./vendor/bin/sail artisan     # Run artisan commands"
echo "  ./vendor/bin/sail artisan test # Run tests"
echo ""
echo "========================================="
echo "📊 Current Status"
echo "========================================="
echo ""

# Check if database file exists
if [ -f "database/sql/nxm_assessment_2023.sql" ]; then
    echo "✅ SQL file found"
    
    # Check if database exists
    DB_EXISTS=$(/bin/bash ./vendor/bin/sail exec mariadb mariadb -u sail -ppassword -e "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'nxm_assessment_2023';" 2>&1)
    
    if echo "$DB_EXISTS" | grep -q "nxm_assessment_2023"; then
        echo "✅ Database 'nxm_assessment_2023' exists"
        
        # Check table count
        TABLE_COUNT=$(/bin/bash ./vendor/bin/sail exec mariadb mariadb -u sail -ppassword nxm_assessment_2023 -e "SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = 'nxm_assessment_2023';" -N 2>&1)
        
        if [ ! -z "$TABLE_COUNT" ] && [ "$TABLE_COUNT" != "0" ]; then
            echo "✅ Database has $TABLE_COUNT tables"
            echo ""
            echo "🎉 Setup is complete! Ready for Milestone 2!"
        else
            echo "⚠️  Database exists but is empty. Please import the SQL file using:"
            echo "   ./db.sh import"
        fi
    else
        echo "⚠️  Database 'nxm_assessment_2023' not found. Please create and import."
    fi
else
    echo "⚠️  SQL file not found at: database/sql/nxm_assessment_2023.sql"
    echo "   Please place the file there and run the import commands above."
fi

echo ""
echo "========================================="
echo "For detailed instructions, see:"
echo "  MILESTONE_1_COMPLETE.md"
echo "========================================="
echo ""
