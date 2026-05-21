-- ── E-Commerce Database ────────────────────────────────────
CREATE DATABASE IF NOT EXISTS ecommerce_db;
USE ecommerce_db;

-- ── Users Table ────────────────────────────────────────────
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ── Categories Table ───────────────────────────────────────
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    image VARCHAR(255)
);

-- ── Products Table ─────────────────────────────────────────
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    old_price DECIMAL(10,2),
    image VARCHAR(255),
    stock INT DEFAULT 100,
    rating DECIMAL(2,1) DEFAULT 4.0,
    featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- ── Cart Table ─────────────────────────────────────────────
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- ── Orders Table ───────────────────────────────────────────
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending','paid','shipped','delivered') DEFAULT 'pending',
    razorpay_order_id VARCHAR(255),
    razorpay_payment_id VARCHAR(255),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- ── Order Items Table ──────────────────────────────────────
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT,
    price DECIMAL(10,2),
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- ── Sample Categories ──────────────────────────────────────
INSERT INTO categories (name, image) VALUES
('Electronics', 'electronics.jpg'),
('Fashion', 'fashion.jpg'),
('Home & Kitchen', 'home.jpg'),
('Books', 'books.jpg');

-- ── Sample Products ────────────────────────────────────────
INSERT INTO products (category_id, name, description, price, old_price, image, stock, rating, featured) VALUES
(1, 'Wireless Bluetooth Earphones', 'Premium sound quality with 20hr battery life. Noise cancellation technology.', 1299, 2999, 'earphones.jpg', 50, 4.5, 1),
(1, 'Smart LED Desk Lamp', 'Touch control, 3 color modes, USB charging port built-in.', 899, 1500, 'lamp.jpg', 30, 4.2, 1),
(1, 'Mechanical Gaming Keyboard', 'RGB backlit, tactile switches, anti-ghosting technology.', 2499, 4999, 'keyboard.jpg', 20, 4.7, 1),
(2, 'Classic Cotton T-Shirt', 'Premium 100% cotton, available in multiple colors, comfortable fit.', 399, 799, 'tshirt.jpg', 100, 4.0, 0),
(2, 'Casual Denim Jacket', 'Stylish denim jacket, perfect for all seasons.', 1599, 2999, 'jacket.jpg', 40, 4.3, 1),
(3, 'Stainless Steel Water Bottle', '1 litre, keeps water cold for 24hrs, leak-proof design.', 499, 899, 'bottle.jpg', 80, 4.6, 0),
(3, 'Non-Stick Cookware Set', '5-piece set, granite coating, suitable for all cooktops.', 2199, 3999, 'cookware.jpg', 25, 4.4, 1),
(4, 'Python Programming Book', 'Complete guide to Python for beginners with 500+ exercises.', 599, 999, 'python_book.jpg', 60, 4.8, 0),
(4, 'Data Science Handbook', 'Master data science with real world projects and case studies.', 799, 1299, 'ds_book.jpg', 45, 4.7, 1),
(1, 'USB-C Fast Charger 65W', 'Universal fast charger compatible with all devices.', 799, 1499, 'charger.jpg', 70, 4.3, 0);
