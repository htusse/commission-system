#!/bin/bash

# Helper script to access MariaDB via Sail

cd "$(dirname "$0")"

if [ "$1" == "show-databases" ]; then
    echo "📋 Available Databases:"
    /bin/bash ./vendor/bin/sail exec mariadb mariadb -u sail -ppassword -e "SHOW DATABASES;"
    
elif [ "$1" == "show-tables" ]; then
    DB="${2:-nxm_assessment_2023}"
    echo "📋 Tables in '$DB':"
    /bin/bash ./vendor/bin/sail exec mariadb mariadb -u sail -ppassword "$DB" -e "SHOW TABLES;"
    
elif [ "$1" == "create-db" ]; then
    echo "🔨 Creating databases..."
    /bin/bash ./vendor/bin/sail exec mariadb mariadb -u sail -ppassword -e "CREATE DATABASE IF NOT EXISTS nxm_assessment_2023;"
    /bin/bash ./vendor/bin/sail exec mariadb mariadb -u sail -ppassword -e "CREATE DATABASE IF NOT EXISTS nxm_assessment_2023_test;"
    echo "✅ Databases created!"
    
elif [ "$1" == "import" ]; then
    SQL_FILE="${2:-database/sql/nxm_assessment_2023.sql}"
    
    if [ ! -f "$SQL_FILE" ]; then
        echo "❌ Error: SQL file not found at: $SQL_FILE"
        echo ""
        echo "Please place nxm_assessment_2023.sql in database/sql/ directory"
        exit 1
    fi
    
    echo "📥 Importing SQL file: $SQL_FILE"
    echo "   → Into database: nxm_assessment_2023"
    
    /bin/bash ./vendor/bin/sail exec -T mariadb mariadb -u sail -ppassword nxm_assessment_2023 < "$SQL_FILE"
    
    if [ $? -eq 0 ]; then
        echo "✅ Import successful!"
        echo ""
        echo "📊 Verifying import:"
        /bin/bash ./vendor/bin/sail exec mariadb mariadb -u sail -ppassword nxm_assessment_2023 -e "SHOW TABLES;"
    else
        echo "❌ Import failed!"
        exit 1
    fi
    
    # Also import to test database
    echo ""
    echo "📥 Importing to test database..."
    /bin/bash ./vendor/bin/sail exec -T mariadb mariadb -u sail -ppassword nxm_assessment_2023_test < "$SQL_FILE"
    
    if [ $? -eq 0 ]; then
        echo "✅ Test database import successful!"
    fi
    
elif [ "$1" == "console" ]; then
    DB="${2:-nxm_assessment_2023}"
    echo "🔌 Connecting to database: $DB"
    echo "   Type 'exit' or press Ctrl+D to quit"
    echo ""
    /bin/bash ./vendor/bin/sail exec mariadb mariadb -u sail -ppassword "$DB"
    
else
    echo "========================================="
    echo "📦 MariaDB Helper Script"
    echo "========================================="
    echo ""
    echo "Usage: ./db.sh [command] [options]"
    echo ""
    echo "Commands:"
    echo "  show-databases              List all databases"
    echo "  show-tables [db_name]       Show tables in database (default: nxm_assessment_2023)"
    echo "  create-db                   Create nxm_assessment_2023 and test databases"
    echo "  import [sql_file]           Import SQL file (default: database/sql/nxm_assessment_2023.sql)"
    echo "  console [db_name]           Open MariaDB console (default: nxm_assessment_2023)"
    echo ""
    echo "Examples:"
    echo "  ./db.sh show-databases"
    echo "  ./db.sh show-tables"
    echo "  ./db.sh create-db"
    echo "  ./db.sh import"
    echo "  ./db.sh import database/sql/myfile.sql"
    echo "  ./db.sh console"
    echo "  ./db.sh console nxm_assessment_2023_test"
    echo ""
    echo "========================================="
    echo "Note: Make sure Sail is running first!"
    echo "  ./vendor/bin/sail up -d"
    echo "========================================="
fi
