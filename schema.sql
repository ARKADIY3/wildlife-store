-- Fixik Computer Shop Database Schema
-- База данных: fixik_bd

CREATE DATABASE IF NOT EXISTS fixik_bd CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fixik_bd;

-- Таблица пользователей
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Таблица категорий
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT
);

-- Таблица товаров (компьютеров)
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category_id INT,
    processor VARCHAR(100),
    ram VARCHAR(50),
    storage VARCHAR(100),
    graphics VARCHAR(100),
    image VARCHAR(255) DEFAULT 'default.png',
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Таблица заказов
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    address TEXT,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Таблица элементов заказа
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- Таблица корзины
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    quantity INT DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Таблица обратной связи
CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- ТЕСТОВЫЕ ДАННЫЕ
-- ==========================================

-- Пользователи (пароль для обоих: 123456)
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@fixik.ru', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('user', 'user@fixik.ru', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- Категории
INSERT INTO categories (name, description) VALUES
('Игровые компьютеры', 'Мощные компьютеры для геймеров'),
('Офисные компьютеры', 'Компьютеры для работы и учебы'),
('Рабочие станции', 'Профессиональные компьютеры для работы с графикой и видео'),
('Компактные ПК', 'Миникомпьютеры и неттопы');

-- Товары (компьютеры)
INSERT INTO products (name, description, price, category_id, processor, ram, storage, graphics, image, stock) VALUES
('Fixik Gaming Pro', 'Мощный игровой компьютер для требовательных игр', 89990.00, 1, 'Intel Core i7-13700K', '32GB DDR5', '1TB NVMe SSD', 'NVIDIA RTX 4070', 'gaming_pro.png', 10),
('Fixik Gaming Ultra', 'Топовая конфигурация для киберспорта', 149990.00, 1, 'Intel Core i9-13900K', '64GB DDR5', '2TB NVMe SSD', 'NVIDIA RTX 4090', 'gaming_ultra.png', 5),
('Fixik Gaming Start', 'Игровой ПК начального уровня', 54990.00, 1, 'AMD Ryzen 5 5600X', '16GB DDR4', '512GB NVMe SSD', 'NVIDIA RTX 3060', 'gaming_start.png', 15),
('Fixik Office Basic', 'Базовый офисный компьютер', 29990.00, 2, 'Intel Core i3-12100', '8GB DDR4', '256GB SSD', 'Intel UHD 730', 'office_basic.png', 20),
('Fixik Office Pro', 'Продвинутый офисный компьютер', 44990.00, 2, 'Intel Core i5-12400', '16GB DDR4', '512GB NVMe SSD', 'Intel UHD 730', 'office_pro.png', 12),
('Fixik Workstation', 'Рабочая станция для 3D и видео', 199990.00, 3, 'AMD Ryzen 9 7950X', '128GB DDR5', '4TB NVMe SSD', 'NVIDIA RTX 4080', 'workstation.png', 3),
('Fixik Creator', 'Компьютер для контент-мейкеров', 124990.00, 3, 'Intel Core i7-13700K', '64GB DDR5', '2TB NVMe SSD', 'NVIDIA RTX 4070 Ti', 'creator.png', 7),
('Fixik Mini', 'Компактный мини-ПК', 39990.00, 4, 'Intel Core i5-12400', '16GB DDR4', '512GB NVMe SSD', 'Intel UHD 730', 'mini.png', 8),
('Fixik Nano', 'Ультракомпактный неттоп', 24990.00, 4, 'Intel Core i3-12100T', '8GB DDR4', '256GB SSD', 'Intel UHD 730', 'nano.png', 25);
