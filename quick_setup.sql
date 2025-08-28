-- Quick setup for sevakaro.in volunteer tables
-- Run this in phpMyAdmin SQL tab

-- Villages Table
CREATE TABLE IF NOT EXISTS villages (
    id int(11) NOT NULL AUTO_INCREMENT,
    village_name varchar(100) NOT NULL,
    PRIMARY KEY (id)
);

INSERT IGNORE INTO villages (village_name) VALUES 
('Mustafabad'), ('Saran'), ('Delhi'), ('Gurgaon'), ('Other');

-- Occupations Table
CREATE TABLE IF NOT EXISTS occupations (
    id int(11) NOT NULL AUTO_INCREMENT,
    occupation_name varchar(100) NOT NULL,
    PRIMARY KEY (id)
);

INSERT IGNORE INTO occupations (occupation_name) VALUES 
('farmer'), ('teacher'), ('doctor'), ('engineer'), ('other');

-- Seva Interests Table
CREATE TABLE IF NOT EXISTS seva_interests (
    id int(11) NOT NULL AUTO_INCREMENT,
    seva_name varchar(100) NOT NULL,
    PRIMARY KEY (id)
);

INSERT IGNORE INTO seva_interests (seva_name) VALUES 
('Help'), ('Teaching'), ('Medical'), ('Food Distribution'), ('Other');

-- Items Table
CREATE TABLE IF NOT EXISTS items (
    id int(11) NOT NULL AUTO_INCREMENT,
    item_name varchar(100) NOT NULL,
    PRIMARY KEY (id)
);

INSERT IGNORE INTO items (item_name) VALUES 
('Clothes'), ('Blanket'), ('Shoes'), ('Utensils'), ('Bag'), ('Other');

-- Cities Table
CREATE TABLE IF NOT EXISTS cities (
    id int(11) NOT NULL AUTO_INCREMENT,
    city_name varchar(100) NOT NULL,
    PRIMARY KEY (id)
);

INSERT IGNORE INTO cities (city_name) VALUES 
('Delhi'), ('Mumbai'), ('Gurgaon'), ('Noida'), ('Other');

-- States Table
CREATE TABLE IF NOT EXISTS states (
    id int(11) NOT NULL AUTO_INCREMENT,
    state_name varchar(100) NOT NULL,
    PRIMARY KEY (id)
);

INSERT IGNORE INTO states (state_name) VALUES 
('Delhi'), ('Haryana'), ('Uttar Pradesh'), ('Maharashtra'), ('Other');

-- Countries Table
CREATE TABLE IF NOT EXISTS countries (
    id int(11) NOT NULL AUTO_INCREMENT,
    country_name varchar(100) NOT NULL,
    PRIMARY KEY (id)
);

INSERT IGNORE INTO countries (country_name) VALUES 
('India'), ('USA'), ('Canada'), ('UK'), ('Other');

-- Main Volunteers Table
CREATE TABLE IF NOT EXISTS volunteers (
    id int(11) NOT NULL AUTO_INCREMENT,
    name varchar(255) NOT NULL,
    mobile varchar(15) DEFAULT NULL,
    email varchar(255) DEFAULT NULL,
    village_id int(11) DEFAULT NULL,
    city_id int(11) DEFAULT NULL,
    state_id int(11) DEFAULT NULL,
    country_id int(11) DEFAULT NULL,
    occupation_id int(11) DEFAULT NULL,
    seva_interest_id int(11) DEFAULT NULL,
    items_brought text DEFAULT NULL,
    created_at timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (id)
);

-- Check if all tables created successfully
SELECT 
    'villages' as table_name, COUNT(*) as records FROM villages
UNION ALL SELECT 
    'occupations' as table_name, COUNT(*) as records FROM occupations
UNION ALL SELECT 
    'seva_interests' as table_name, COUNT(*) as records FROM seva_interests
UNION ALL SELECT 
    'items' as table_name, COUNT(*) as records FROM items
UNION ALL SELECT 
    'cities' as table_name, COUNT(*) as records FROM cities
UNION ALL SELECT 
    'states' as table_name, COUNT(*) as records FROM states
UNION ALL SELECT 
    'countries' as table_name, COUNT(*) as records FROM countries
UNION ALL SELECT 
    'volunteers' as table_name, COUNT(*) as records FROM volunteers;


