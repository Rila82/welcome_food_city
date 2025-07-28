<?php
session_start();
include('../config/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $product_id = (int)$_POST['id'];

    // Fetch product from DB
    $stmt = $conn->prepare("SELECT id, name, price, image, stock_qty FROM products WHERE id = ? ");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found.']);
        exit;
    }

    // Initialize cart if not exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // If already in cart
    if (isset($_SESSION['cart'][$product_id])) {
        // Increase only if less than stock
        if ($_SESSION['cart'][$product_id]['quantity'] < $product['stock_qty']) {
            $_SESSION['cart'][$product_id]['quantity'] += 1;
            echo json_encode(['success' => true, 'message' => 'Quantity increased in cart.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Reached stock limit.']);
        }
    } else {
        // Add new product to cart with correct quantity and stock
        $_SESSION['cart'][$product_id] = [
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $product['image'],
            'quantity' => 1,
            'stock_qty' => $product['stock_qty']
        ];
        echo json_encode(['success' => true, 'message' => 'Added to cart!']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
