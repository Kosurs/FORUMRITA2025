CREATE DATABASE IF NOT EXISTS car_shop;
USE car_shop;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    brand VARCHAR(100) NOT NULL,
    model VARCHAR(100) NOT NULL,
    year INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    description TEXT
);

-- Exemplo de inserção
INSERT INTO cars (brand, model, year, price, image, description) VALUES
('Toyota', 'Corolla', 2020, 85000.00, 'images/corolla.jpg', 'Toyota Corolla 2020, completo, automático, único dono.'),
('Honda', 'Civic', 2019, 90000.00, 'images/civic.jpg', 'Honda Civic 2019, excelente estado, baixa km.');
