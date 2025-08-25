-- Copy-paste this SQL in phpMyAdmin SQL tab

-- 1. Villages Table
CREATE TABLE villages (
    id int(11) NOT NULL AUTO_INCREMENT,
    village_name varchar(100) NOT NULL,
    PRIMARY KEY (id)
);

INSERT INTO villages (village_name) VALUES 
('Mustafabad'), ('Saran'), ('Delhi'), ('Gurgaon'), ('Other');

-- 2. Occupations Table  
CREATE TABLE occupations (
    id int(11) NOT NULL AUTO_INCREMENT,
    occupation_name varchar(100) NOT NULL,
    PRIMARY KEY (id)
);

INSERT INTO occupations (occupation_name) VALUES 
('farmer'), ('teacher'), ('doctor'), ('engineer'), ('other');

-- 3. Seva Interests Table
CREATE TABLE seva_interests (
    id int(11) NOT NULL AUTO_INCREMENT,
    seva_name varchar(100) NOT NULL,
    PRIMARY KEY (id)
);

INSERT INTO seva_interests (seva_name) VALUES 
('Help'), ('Teaching'), ('Medical'), ('Food Distribution'), ('Other');

-- 4. Items Table
CREATE TABLE items (
    id int(11) NOT NULL AUTO_INCREMENT,
    item_name varchar(100) NOT NULL,
    PRIMARY KEY (id)
);

INSERT INTO items (item_name) VALUES 
('Clothes'), ('Blanket'), ('Shoes'), ('Utensils'), ('Bag'), ('Other');

-- 5. Cities Table
CREATE TABLE cities (
    id int(11) NOT NULL AUTO_INCREMENT,
    city_name varchar(100) NOT NULL,
    PRIMARY KEY (id)
);

INSERT INTO cities (city_name) VALUES 
('Delhi'), ('Mumbai'), ('Gurgaon'), ('Noida'), ('Other');

-- 6. States Table
CREATE TABLE states (
    id int(11) NOT NULL AUTO_INCREMENT,
    state_name varchar(100) NOT NULL,
    PRIMARY KEY (id)
);

INSERT INTO states (state_name) VALUES 
('Delhi'), ('Haryana'), ('Uttar Pradesh'), ('Maharashtra'), ('Other');

-- 7. Countries Table
CREATE TABLE countries (
    id int(11) NOT NULL AUTO_INCREMENT,
    country_name varchar(100) NOT NULL,
    PRIMARY KEY (id)
);

INSERT INTO countries (country_name) VALUES 
('India'), ('USA'), ('Canada'), ('UK'), ('Other');

-- 8. Main Volunteers Table
CREATE TABLE volunteers (
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
    PRIMARY KEY (id),
    KEY fk_volunteers_village (village_id),
    KEY fk_volunteers_occupation (occupation_id),
    KEY fk_volunteers_seva (seva_interest_id),
    KEY fk_volunteers_city (city_id),
    KEY fk_volunteers_state (state_id),
    KEY fk_volunteers_country (country_id),
    CONSTRAINT fk_volunteers_city FOREIGN KEY (city_id) REFERENCES cities (id),
    CONSTRAINT fk_volunteers_country FOREIGN KEY (country_id) REFERENCES countries (id),
    CONSTRAINT fk_volunteers_occupation FOREIGN KEY (occupation_id) REFERENCES occupations (id),
    CONSTRAINT fk_volunteers_seva FOREIGN KEY (seva_interest_id) REFERENCES seva_interests (id),
    CONSTRAINT fk_volunteers_state FOREIGN KEY (state_id) REFERENCES states (id),
    CONSTRAINT fk_volunteers_village FOREIGN KEY (village_id) REFERENCES villages (id)
);

-- Verify all tables created
SHOW TABLES;

