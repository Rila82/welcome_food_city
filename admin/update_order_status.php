<?php
session_start();
include('../config/db.php');

// ✅ Admin access check
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

// ✅ Get order ID and new status
$orderId = $_GET['id'] ?? 0;
$status = $_GET['status'] ?? '';

$allowedStatuses = ['Completed', 'Cancelled'];

if (!$orderId || !in_array($status, $allowedStatuses)) {
    $_SESSION['error_msg'] = "Invalid request!";
    header("Location: manage_orders.php");
    exit;
}

// ✅ Update order status (and set completed_at or cancelled_at)
if ($status === 'Completed') {
    $stmt = $conn->prepare("UPDATE orders SET status = ?, completed_at = NOW(), cancelled_at = NULL WHERE id = ?");
} elseif ($status === 'Cancelled') {
    $stmt = $conn->prepare("UPDATE orders SET status = ?, cancelled_at = NOW(), completed_at = NULL WHERE id = ?");
}

$stmt->bind_param("si", $status, $orderId);

if ($stmt->execute()) {
    $_SESSION['success_msg'] = "Order #$orderId marked as $status.";
} else {
    $_SESSION['error_msg'] = "Failed to update order.";
}

header("Location: manage_orders.php");
exit;
?>
