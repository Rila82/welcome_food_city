<?php
session_start();
include('../config/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['cart'])) {
    $cart = $_SESSION['cart'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $zone_id = $_POST['zone_id'];
    $zone_name = $_POST['zone_name'];
    $shipping_fee = (float)$_POST['shipping_fee'];
    $payment_method = $_POST['payment_method'];

    // Calculate total
    $subtotal = 0;
    foreach ($cart as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
    $total_amount = $subtotal + $shipping_fee;

    // Insert into orders
    $stmt = $conn->prepare("INSERT INTO orders (name, phone, address, zone, shipping_fee, total_amount, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssdss", $name, $phone, $address, $zone_name, $shipping_fee, $total_amount, $payment_method);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // Insert order items
    $stmt_items = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($cart as $product_id => $item) {
        $qty = $item['quantity'];
        $price = $item['price'];
        $stmt_items->bind_param("iiid", $order_id, $product_id, $qty, $price);
        $stmt_items->execute();

        // Update product stock
        $conn->query("UPDATE products SET stock_qty = stock_qty - $qty WHERE id = $product_id");
    }

    // Clear cart
    unset($_SESSION['cart']);

    header("Location: order_success.php?order_id=$order_id");
    exit;
} else {
    echo "<div class='container mt-5 alert alert-danger'>Invalid request or empty cart.</div>";
}
?>
