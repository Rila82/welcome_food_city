<?php
session_start();
include('../config/db.php');

// 🔐 Only allow logged-in admins
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

// ✅ Validate product ID
$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    die("Invalid product ID.");
}

// ✅ Get current status
$stmt = $conn->prepare("SELECT status FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if (!$row = $result->fetch_assoc()) {
    die("Product not found.");
}

$currentStatus = $row['status'];
$newStatus = $currentStatus === 'active' ? 'hold' : 'active';

// ✅ Update status
$update = $conn->prepare("UPDATE products SET status = ? WHERE id = ?");
$update->bind_param("si", $newStatus, $id);
$update->execute();

// ✅ Redirect back
header("Location: manage_products.php");
exit;
?>
