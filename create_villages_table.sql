-- Create villages table if it doesn't exist
CREATE TABLE IF NOT EXISTS `villages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `village_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `village_name` (`village_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert some sample villages
INSERT IGNORE INTO `villages` (`village_name`) VALUES 
('Delhi'),
('Mumbai'),
('Bangalore'),
('Chennai'),
('Kolkata'),
('Hyderabad'),
('Pune'),
('Ahmedabad'),
('Jaipur'),
('Lucknow');

