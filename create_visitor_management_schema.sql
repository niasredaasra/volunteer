-- Database schema for visitor management with visit history
-- This extends the existing volunteers table to support visit tracking

-- First, create a separate visitors table (different from volunteers)
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

-- Create visit history table
CREATE TABLE IF NOT EXISTS visitor_visits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visitor_id INT NOT NULL,
    items_brought JSON COMMENT 'JSON array of items brought',
    remarks TEXT,
    visit_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Foreign key constraint
    FOREIGN KEY (visitor_id) REFERENCES visitors(id) ON DELETE CASCADE,
    
    -- Index for visitor lookup
    INDEX idx_visitor_id (visitor_id),
    INDEX idx_visit_date (visit_date)
);

-- Create index for faster mobile lookups
CREATE INDEX IF NOT EXISTS idx_visitors_mobile ON visitors(mobile);

-- Insert some sample data for testing
INSERT IGNORE INTO visitors (name, mobile, email, phone, village_id, city_id, state_id, country_id, occupation_id, seva_interest_id, dob) VALUES
('John Doe', '9876543210', 'john@example.com', '9876543211', 1, 1, 1, 1, 1, 1, '1990-01-15'),
('Jane Smith', '9876543220', 'jane@example.com', '9876543221', 2, 2, 2, 1, 2, 2, '1985-03-22');

-- Insert some sample visit history
INSERT IGNORE INTO visitor_visits (visitor_id, items_brought, remarks) VALUES
(1, '["Clothes", "Blanket"]', 'First visit - brought winter items'),
(1, '["Food", "Medicine"]', 'Second visit - brought essentials'),
(2, '["Books", "Stationery"]', 'Educational materials donation');

-- Show success message
SELECT 'Visitor management schema created successfully!' as Status;


