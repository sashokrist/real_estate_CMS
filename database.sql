-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS real_estate;
USE real_estate;

-- Drop existing tables if they exist (in correct order due to foreign key constraints)
DROP TABLE IF EXISTS property_features;
DROP TABLE IF EXISTS properties;
DROP TABLE IF EXISTS news;
DROP TABLE IF EXISTS renovating_services;

-- Create properties table
CREATE TABLE properties (
    id VARCHAR(50) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    location VARCHAR(255) NOT NULL,
    size VARCHAR(50) NOT NULL,
    year_built VARCHAR(4),
    image_url VARCHAR(255) NOT NULL,
    bedrooms INT NOT NULL,
    bathrooms INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create property_features table
CREATE TABLE property_features (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id VARCHAR(50) NOT NULL,
    feature VARCHAR(255) NOT NULL,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create news table
CREATE TABLE news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create renovating_services table
CREATE TABLE renovating_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    service_type VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data for properties
INSERT INTO properties (id, title, description, price, location, size, year_built, image_url, bedrooms, bathrooms) VALUES
('modern-villa', 'Modern Villa', 'A stunning modern villa with panoramic views and luxurious amenities. Perfect for those seeking a contemporary lifestyle with all the comforts of modern living.', 650000.00, '123 Luxury Lane, Beverly Hills', '3,500 sq ft', '2020', 'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7', 3, 2),
('urban-apartment', 'Urban Apartment', 'Stylish urban apartment in the heart of the city. Features modern design and convenient access to all city amenities.', 420000.00, '456 Downtown Ave, City Center', '1,200 sq ft', '2018', 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c', 2, 1),
('suburban-home', 'Suburban Home', 'Spacious family home in a quiet suburban neighborhood. Perfect for growing families with excellent schools nearby.', 530000.00, '789 Suburban St, Green Valley', '2,800 sq ft', '2015', 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c', 4, 3);

-- Insert sample data for property features
INSERT INTO property_features (property_id, feature) VALUES
('modern-villa', '3 Bedrooms'),
('modern-villa', '2 Bathrooms'),
('modern-villa', 'Swimming Pool'),
('modern-villa', 'Smart Home System'),
('modern-villa', 'Garden'),
('modern-villa', 'Garage'),
('urban-apartment', '2 Bedrooms'),
('urban-apartment', '1 Bathroom'),
('urban-apartment', 'City View'),
('urban-apartment', 'Fitness Center'),
('urban-apartment', 'Concierge'),
('urban-apartment', 'Parking'),
('suburban-home', '4 Bedrooms'),
('suburban-home', '3 Bathrooms'),
('suburban-home', 'Large Garden'),
('suburban-home', 'Garage'),
('suburban-home', 'Basement'),
('suburban-home', 'Deck');

-- Insert sample data for news
INSERT INTO news (title, content, image_url) VALUES
('Market Trends', 'Real estate market shows steady growth with 5% increase in property values this quarter.', 'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7'),
('New Development', 'New luxury apartment complex announced in downtown area, featuring modern amenities.', 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c'),
('Renovation Tips', 'Expert tips for increasing home value through strategic renovation projects.', 'https://images.unsplash.com/photo-1599423300746-b62533397364'),
('Investment Guide', 'How to make smart real estate investments in the current market conditions.', 'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7'),
('Green Homes', 'Sustainable building practices gaining popularity among new home buyers.', 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c');

-- Insert sample data for renovating services
INSERT INTO renovating_services (title, description, image_url, service_type) VALUES
('Modern Kitchen Renovations', 'We transform outdated kitchens into beautiful, functional spaces tailored to your cooking and lifestyle needs.', 'https://images.unsplash.com/photo-1599423300746-b62533397364', 'kitchen'),
('Elegant Bathroom Makeovers', 'From spa-like retreats to practical upgrades, we handle complete bathroom overhauls with expert design and craftsmanship.', 'https://images.unsplash.com/photo-1599423300746-b62533397364', 'bathroom'),
('Complete Home Renovations', 'Give your entire house a new life. We modernize layouts, interiors, and exteriors to boost comfort and value.', 'https://images.unsplash.com/photo-1599423300746-b62533397364', 'full'); 