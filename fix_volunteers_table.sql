-- Fix volunteers table structure to match PHP code expectations
-- This script will update the existing table to use proper foreign keys

-- Step 1: Add new columns for proper foreign key relationships
ALTER TABLE volunteers ADD COLUMN village_id INT NULL AFTER name;

-- Step 2: Update existing records to link with villages table
-- This will try to match existing village names with the villages table
UPDATE volunteers v 
JOIN villages g ON v.village = g.village_name 
SET v.village_id = g.id 
WHERE v.village IS NOT NULL AND v.village != '';

-- Step 3: Remove the old village column (only after confirming data migration)
-- ALTER TABLE volunteers DROP COLUMN village;

-- Step 4: Add foreign key constraints
ALTER TABLE volunteers ADD CONSTRAINT fk_volunteers_village 
FOREIGN KEY (village_id) REFERENCES villages(id);

ALTER TABLE volunteers ADD CONSTRAINT fk_volunteers_occupation 
FOREIGN KEY (occupation_id) REFERENCES occupations(id);

ALTER TABLE volunteers ADD CONSTRAINT fk_volunteers_seva 
FOREIGN KEY (seva_interest_id) REFERENCES seva_interests(id);

-- Step 5: Show the updated table structure
DESCRIBE volunteers;

