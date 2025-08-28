-- Quick table creation for Hostinger
-- Use this after fixing database credentials

-- Villages
CREATE TABLE villages (id INT AUTO_INCREMENT PRIMARY KEY, village_name VARCHAR(100));
INSERT INTO villages (village_name) VALUES ('Mustafabad'), ('Saran'), ('Delhi'), ('Gurgaon'), ('Other');

-- Occupations  
CREATE TABLE occupations (id INT AUTO_INCREMENT PRIMARY KEY, occupation_name VARCHAR(100));
INSERT INTO occupations (occupation_name) VALUES ('farmer'), ('teacher'), ('doctor'), ('engineer'), ('other');

-- Seva Interests
CREATE TABLE seva_interests (id INT AUTO_INCREMENT PRIMARY KEY, seva_name VARCHAR(100));
INSERT INTO seva_interests (seva_name) VALUES ('Help'), ('Teaching'), ('Medical'), ('Food Distribution'), ('Other');

-- Items
CREATE TABLE items (id INT AUTO_INCREMENT PRIMARY KEY, item_name VARCHAR(100));
INSERT INTO items (item_name) VALUES ('Clothes'), ('Blanket'), ('Shoes'), ('Utensils'), ('Bag'), ('Other');

-- Cities
CREATE TABLE cities (id INT AUTO_INCREMENT PRIMARY KEY, city_name VARCHAR(100));
INSERT INTO cities (city_name) VALUES ('Delhi'), ('Mumbai'), ('Gurgaon'), ('Noida'), ('Other');

-- States
CREATE TABLE states (id INT AUTO_INCREMENT PRIMARY KEY, state_name VARCHAR(100));
INSERT INTO states (state_name) VALUES ('Delhi'), ('Haryana'), ('Uttar Pradesh'), ('Maharashtra'), ('Other');

-- Countries
CREATE TABLE countries (id INT AUTO_INCREMENT PRIMARY KEY, country_name VARCHAR(100));
INSERT INTO countries (country_name) VALUES ('India'), ('USA'), ('Canada'), ('UK'), ('Other');

-- Volunteers (Main table)
CREATE TABLE volunteers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    mobile VARCHAR(15),
    email VARCHAR(255),
    village_id INT, city_id INT, state_id INT, country_id INT,
    occupation_id INT, seva_interest_id INT,
    items_brought TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Show all tables to verify
SHOW TABLES;


