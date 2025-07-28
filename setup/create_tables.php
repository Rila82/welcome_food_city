<?php
$host = "localhost";
$user = "root";
$pass = "200113302248Rila";
$dbname = "welcome_food_city";

// Connect to MySQL
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL to create all tables
$sql = "

-- USERS TABLE
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    phone VARCHAR(15),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- PRODUCTS TABLE
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    brand VARCHAR(100),
    barcode VARCHAR(100),
    category VARCHAR(100),
    sub_category VARCHAR(100),
    item_size VARCHAR(50), -- e.g., 250g, 500ml, 6ft
    description TEXT,
    price DECIMAL(10,2),
    image VARCHAR(255),
    stock_qty INT,
    offer_percentage DECIMAL(5,2) DEFAULT 0,
    offer_start DATETIME DEFAULT NULL,
    offer_end DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ORDERS TABLE
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    zone VARCHAR(100),
    shipping_fee DECIMAL(10,2),
    total_amount DECIMAL(10,2),
    payment_method VARCHAR(20) DEFAULT 'COD',
    status VARCHAR(20) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ORDER ITEMS TABLE
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT,
    price DECIMAL(10,2),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- ADMINS TABLE
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255)
);

-- DELIVERY ZONES TABLE
CREATE TABLE IF NOT EXISTS delivery_zones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    area_name VARCHAR(100),
    delivery_fee DECIMAL(10,2)
);
";

if ($conn->multi_query($sql)) {
    echo "✅ All tables created successfully.";
} else {
    echo "❌ Error creating tables: " . $conn->error;
}

$conn->close();
?>
