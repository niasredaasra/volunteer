<?php
require_once 'api/db.php';

echo "=== Database Fix Script ===\n";
echo "This script will fix the volunteers table structure to match the PHP code.\n\n";

try {
    $conn = db();
    echo "✓ Database connection successful\n\n";
    
    // Step 1: Add village_id column
    echo "Step 1: Adding village_id column...\n";
    $result = $conn->query("ALTER TABLE volunteers ADD COLUMN village_id INT NULL AFTER name");
    if ($result) {
        echo "✓ village_id column added successfully\n";
    } else {
        echo "⚠ village_id column might already exist or there was an issue\n";
    }
    
    // Step 2: Update existing records to link with villages table
    echo "\nStep 2: Updating existing records...\n";
    $result = $conn->query("UPDATE volunteers v 
        JOIN villages g ON v.village = g.village_name 
        SET v.village_id = g.id 
        WHERE v.village IS NOT NULL AND v.village != ''");
    
    if ($result) {
        $affected = $conn->affected_rows;
        echo "✓ Updated $affected records with village_id\n";
    } else {
        echo "⚠ No records updated or there was an issue\n";
    }
    
    // Step 3: Add foreign key constraints
    echo "\nStep 3: Adding foreign key constraints...\n";
    
    // Village foreign key
    try {
        $result = $conn->query("ALTER TABLE volunteers ADD CONSTRAINT fk_volunteers_village 
            FOREIGN KEY (village_id) REFERENCES villages(id)");
        if ($result) {
            echo "✓ Village foreign key constraint added\n";
        }
    } catch (Exception $e) {
        echo "⚠ Village foreign key constraint issue: " . $e->getMessage() . "\n";
    }
    
    // Occupation foreign key
    try {
        $result = $conn->query("ALTER TABLE volunteers ADD CONSTRAINT fk_volunteers_occupation 
            FOREIGN KEY (occupation_id) REFERENCES occupations(id)");
        if ($result) {
            echo "✓ Occupation foreign key constraint added\n";
        }
    } catch (Exception $e) {
        echo "⚠ Occupation foreign key constraint issue: " . $e->getMessage() . "\n";
    }
    
    // Seva foreign key
    try {
        $result = $conn->query("ALTER TABLE volunteers ADD CONSTRAINT fk_volunteers_seva 
            FOREIGN KEY (seva_interest_id) REFERENCES seva_interests(id)");
        if ($result) {
            echo "✓ Seva foreign key constraint added\n";
        }
    } catch (Exception $e) {
        echo "⚠ Seva foreign key constraint issue: " . $e->getMessage() . "\n";
    }
    
    // Step 4: Show updated table structure
    echo "\nStep 4: Updated table structure:\n";
    echo "================================\n";
    $result = $conn->query("DESCRIBE volunteers");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo sprintf("%-20s %-20s %-8s %-8s %-8s\n", 
                $row['Field'], $row['Type'], $row['Null'], $row['Key'], $row['Default'] ?? 'NULL');
        }
    }
    
    // Step 5: Test the volunteers API
    echo "\nStep 5: Testing volunteers API...\n";
    $result = $conn->query("SELECT COUNT(*) as count FROM volunteers");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "✓ volunteers table has " . $row['count'] . " records\n";
    }
    
    echo "\n=== Database Fix Complete ===\n";
    echo "Your volunteers table should now work with the PHP code!\n";
    echo "Try submitting the form again to test.\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Please check your database connection and try again.\n";
}
?>

