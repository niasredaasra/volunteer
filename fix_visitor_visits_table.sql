-- Fix visitor_visits table structure to resolve JSON constraint error
-- Run this SQL in phpMyAdmin if you already have the visitor_visits table created

-- Step 1: Check if visitor_visits table exists and what its structure is
DESCRIBE visitor_visits;

-- Step 2: Drop the existing visitor_visits table if it has JSON constraint issues
-- Uncomment the next line only if you want to recreate the table (will lose existing data)
-- DROP TABLE IF EXISTS visitor_visits;

-- Step 3: Create the visitor_visits table with TEXT instead of JSON (more compatible)
CREATE TABLE IF NOT EXISTS visitor_visits_new (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visitor_id INT NOT NULL,
    items_brought TEXT COMMENT 'JSON string of items brought',
    remarks TEXT,
    visit_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Foreign key constraint
    FOREIGN KEY (visitor_id) REFERENCES visitors(id) ON DELETE CASCADE,
    
    -- Index for visitor lookup
    INDEX idx_visitor_id (visitor_id),
    INDEX idx_visit_date (visit_date)
);

-- Step 4: If you have existing data, copy it from old table to new table
-- Uncomment these lines if you need to migrate existing data:
/*
INSERT INTO visitor_visits_new (visitor_id, items_brought, remarks, visit_date)
SELECT visitor_id, items_brought, remarks, visit_date 
FROM visitor_visits;
*/

-- Step 5: Replace the old table with the new one
-- Uncomment these lines after backing up your data:
/*
DROP TABLE visitor_visits;
RENAME TABLE visitor_visits_new TO visitor_visits;
*/

-- Alternative: If table doesn't exist yet, just create it directly
CREATE TABLE IF NOT EXISTS visitor_visits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visitor_id INT NOT NULL,
    items_brought TEXT COMMENT 'JSON string of items brought',
    remarks TEXT,
    visit_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Foreign key constraint  
    FOREIGN KEY (visitor_id) REFERENCES visitors(id) ON DELETE CASCADE,
    
    -- Index for visitor lookup
    INDEX idx_visitor_id (visitor_id),
    INDEX idx_visit_date (visit_date)
);

-- Success message
SELECT 'Visitor visits table structure fixed! JSON constraint error should be resolved.' as Status;
