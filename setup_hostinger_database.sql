-- Run this SQL in Hostinger phpMyAdmin to create all tables
-- This includes the updated structure with all the fixes

-- Villages
CREATE TABLE IF NOT EXISTS villages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    village_name VARCHAR(100) NOT NULL
);
INSERT IGNORE INTO villages (village_name) VALUES 
('Mustafabad'), ('Saran'), ('Delhi'), ('Gurgaon'), ('Other');

-- Cities
CREATE TABLE IF NOT EXISTS cities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    city_name VARCHAR(100) NOT NULL
);
INSERT IGNORE INTO cities (city_name) VALUES 
('Delhi'), ('Mumbai'), ('Gurgaon'), ('Noida'), ('Other');

-- States
CREATE TABLE IF NOT EXISTS states (
    id INT AUTO_INCREMENT PRIMARY KEY,
    state_name VARCHAR(100) NOT NULL
);
INSERT IGNORE INTO states (state_name) VALUES 
('Delhi'), ('Haryana'), ('Uttar Pradesh'), ('Maharashtra'), ('Other');

-- Countries
CREATE TABLE IF NOT EXISTS countries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    country_name VARCHAR(100) NOT NULL
);
INSERT IGNORE INTO countries (country_name) VALUES 
('India'), ('USA'), ('Canada'), ('UK'), ('Other');

-- Occupations
CREATE TABLE IF NOT EXISTS occupations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    occupation_name VARCHAR(100) NOT NULL
);
INSERT IGNORE INTO occupations (occupation_name) VALUES 
('farmer'), ('teacher'), ('doctor'), ('engineer'), ('other');

-- Seva Interests
CREATE TABLE IF NOT EXISTS seva_interests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seva_name VARCHAR(100) NOT NULL
);
INSERT IGNORE INTO seva_interests (seva_name) VALUES 
('Help'), ('Teaching'), ('Medical'), ('Food Distribution'), ('Other');

-- Items
CREATE TABLE IF NOT EXISTS items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(100) NOT NULL
);
INSERT IGNORE INTO items (item_name) VALUES 
('Clothes'), ('Blanket'), ('Shoes'), ('Utensils'), ('Bag'), ('Other');

-- Volunteers (Updated structure with all required columns)
CREATE TABLE IF NOT EXISTS volunteers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    mobile VARCHAR(15),
    email VARCHAR(255),
    village_id INT,
    city_id INT,
    state_id INT,
    country_id INT,
    occupation_id INT,
    seva_interest_id INT,
    phone VARCHAR(15),
    dob DATE,
    remarks TEXT,
    items_brought TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (village_id) REFERENCES villages(id),
    FOREIGN KEY (city_id) REFERENCES cities(id),
    FOREIGN KEY (state_id) REFERENCES states(id),
    FOREIGN KEY (country_id) REFERENCES countries(id),
    FOREIGN KEY (occupation_id) REFERENCES occupations(id),
    FOREIGN KEY (seva_interest_id) REFERENCES seva_interests(id)
);

-- Visitors table (for visitor management)
CREATE TABLE IF NOT EXISTS visitors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    mobile VARCHAR(15) UNIQUE NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(15) COMMENT 'Alternative phone number',
    village_id INT,
    city_id INT,
    state_id INT,
    country_id INT,
    occupation_id INT,
    seva_interest_id INT,
    dob DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign key constraints
    FOREIGN KEY (village_id) REFERENCES villages(id),
    FOREIGN KEY (city_id) REFERENCES cities(id),
    FOREIGN KEY (state_id) REFERENCES states(id),
    FOREIGN KEY (country_id) REFERENCES countries(id),
    FOREIGN KEY (occupation_id) REFERENCES occupations(id),
    FOREIGN KEY (seva_interest_id) REFERENCES seva_interests(id),
    
    -- Index for mobile number lookup
    INDEX idx_mobile (mobile)
);

-- Visitor visits table (for visit history tracking)
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

-- Show success message
SELECT 'All tables created successfully! Ready for Hostinger deployment.' as Status;
