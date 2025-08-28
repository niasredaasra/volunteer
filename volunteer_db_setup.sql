-- Setup tables for volunteer database
-- Copy-paste this in phpMyAdmin SQL tab

-- 1. Villages Table
CREATE TABLE villages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    village_name VARCHAR(100) NOT NULL
);

INSERT INTO villages (village_name) VALUES 
('Mustafabad'), ('Saran'), ('Delhi'), ('Gurgaon'), ('Other');

-- 2. Occupations Table
CREATE TABLE occupations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    occupation_name VARCHAR(100) NOT NULL
);

INSERT INTO occupations (occupation_name) VALUES 
('farmer'), ('teacher'), ('doctor'), ('engineer'), ('other');

-- 3. Seva Interests Table
CREATE TABLE seva_interests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seva_name VARCHAR(100) NOT NULL
);

INSERT INTO seva_interests (seva_name) VALUES 
('Help'), ('Teaching'), ('Medical'), ('Food Distribution'), ('Other');

-- 4. Items Table
CREATE TABLE items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(100) NOT NULL
);

INSERT INTO items (item_name) VALUES 
('Clothes'), ('Blanket'), ('Shoes'), ('Utensils'), ('Bag'), ('Other');

-- 5. Cities Table
CREATE TABLE cities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    city_name VARCHAR(100) NOT NULL
);

INSERT INTO cities (city_name) VALUES 
('Delhi'), ('Mumbai'), ('Gurgaon'), ('Noida'), ('Other');

-- 6. States Table
CREATE TABLE states (
    id INT AUTO_INCREMENT PRIMARY KEY,
    state_name VARCHAR(100) NOT NULL
);

INSERT INTO states (state_name) VALUES 
('Delhi'), ('Haryana'), ('Uttar Pradesh'), ('Maharashtra'), ('Other');

-- 7. Countries Table
CREATE TABLE countries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    country_name VARCHAR(100) NOT NULL
);

INSERT INTO countries (country_name) VALUES 
('India'), ('USA'), ('Canada'), ('UK'), ('Other');

-- 8. Volunteers Table (Main)
CREATE TABLE volunteers (
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
    items_brought TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_village (village_id),
    INDEX idx_city (city_id),
    INDEX idx_state (state_id),
    INDEX idx_country (country_id),
    INDEX idx_occupation (occupation_id),
    INDEX idx_seva (seva_interest_id)
);

-- Verify all tables created
SELECT 
    TABLE_NAME as 'Table Name',
    TABLE_ROWS as 'Records'
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = DATABASE()
ORDER BY TABLE_NAME;


