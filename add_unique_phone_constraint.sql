-- Add unique constraint to prevent duplicate phone numbers in volunteers table
-- Run this SQL in phpMyAdmin to prevent duplicate phone entries

-- First, clean up any existing duplicate phone numbers (optional)
-- This query shows duplicate phone numbers if any exist
SELECT phone, COUNT(*) as count 
FROM volunteers 
WHERE phone IS NOT NULL AND phone != '' 
GROUP BY phone 
HAVING COUNT(*) > 1;

-- If you want to remove duplicates, keep only the first entry for each phone number
-- Uncomment the following lines if you need to clean existing duplicates:
/*
DELETE v1 FROM volunteers v1
INNER JOIN volunteers v2 
WHERE v1.id > v2.id 
AND v1.phone = v2.phone 
AND v1.phone IS NOT NULL 
AND v1.phone != '';
*/

-- Add unique constraint on phone number
-- This will prevent future duplicate phone entries
ALTER TABLE volunteers 
ADD CONSTRAINT unique_phone UNIQUE (phone);

-- Also add unique constraint on mobile if needed
-- Uncomment if you want to make mobile unique too:
/*
ALTER TABLE volunteers 
ADD CONSTRAINT unique_mobile UNIQUE (mobile);
*/

-- Verify the constraint was added
SHOW INDEX FROM volunteers WHERE Key_name = 'unique_phone';

-- Success message
SELECT 'Unique phone constraint added successfully! No duplicate phone numbers will be allowed.' as Status;

