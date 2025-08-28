<?php
// Check if you already have a database for sevakaro.in

echo "=== Database Configuration Check ===\n\n";

// Check if there's an existing config file on sevakaro.in
echo "1. Check Current Database Setup:\n";
echo "   - Login to Hostinger Control Panel\n";
echo "   - Go to Databases section\n";
echo "   - See existing databases\n\n";

echo "2. Options for Volunteer App:\n\n";

echo "Option A: Use Existing Database (if any)\n";
echo "   - Add volunteer tables to existing database\n";
echo "   - Update config.php with existing credentials\n";
echo "   - Run setup_database_hostinger.sql\n\n";

echo "Option B: Create New Database\n";
echo "   - Create: u123456_volunteers\n";
echo "   - Separate database for volunteer management\n";
echo "   - Clean separation from main website\n\n";

echo "3. Recommended Configuration:\n";
echo "   If sevakaro.in has existing database:\n";
echo "   - Use same database\n";
echo "   - Add volunteer tables\n";
echo "   - Single database for entire website\n\n";

echo "4. Config.php Template:\n";
echo "   // For existing database\n";
echo "   define('DB_HOST', 'localhost');\n";
echo "   define('DB_USER', 'u123456_sevakaro');  // Your existing username\n";
echo "   define('DB_PASS', 'your_existing_pass'); // Your existing password\n";
echo "   define('DB_NAME', 'u123456_sevakaro');  // Your existing database\n\n";

echo "=== Next Steps ===\n";
echo "1. Check your existing database credentials\n";
echo "2. Update config.php accordingly\n";
echo "3. Import volunteer tables\n";
echo "4. Test volunteer registration\n";
?>


