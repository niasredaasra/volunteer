@echo off
echo 🧹 Cleaning up unnecessary files...

REM Delete duplicate config files
del config_local.php 2>nul
del config_hostinger.php 2>nul
del config_for_hostinger.php 2>nul
del config_hostinger_template.php 2>nul

REM Delete debug files
del debug_*.php 2>nul
del test_*.php 2>nul
del simple_*.php 2>nul
del manual_db_test.php 2>nul
del check_existing_database.php 2>nul
del populate_sample_data.php 2>nul
del run_db_fix.php 2>nul
del setup_fix.php 2>nul

REM Delete duplicate files
del "api\volunteers - Copy.php" 2>nul

REM Delete old SQL files (keeping only setup_hostinger_database.sql)
del create_all_tables.sql 2>nul
del create_villages_table.sql 2>nul
del hostinger_manual_sql.sql 2>nul
del hostinger_quick_tables.sql 2>nul
del quick_setup.sql 2>nul
del volunteer_db_setup.sql 2>nul
del fix_volunteers_table.sql 2>nul
del add_unique_phone_constraint.sql 2>nul

REM Delete deployment scripts
del auto_deploy_verify.js 2>nul
del deploy_verify.js 2>nul
del verify_all_files.js 2>nul
del setup_deployment.bat 2>nul
del package.json 2>nul

echo ✅ Cleanup completed!
echo.
echo 📁 Essential files remaining:
echo   - index.php
echo   - config.php  
echo   - helpers.php
echo   - .htaccess
echo   - api/ folder
echo   - admin/ folder
echo   - setup_hostinger_database.sql
echo.
echo 🚀 Your project is now clean and ready for deployment!
pause
