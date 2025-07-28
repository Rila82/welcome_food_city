<?php
session_start();
include('../config/db.php');

// Ensure admin access
if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Count orders not seen by admin
$sql = "SELECT COUNT(*) as new_orders FROM orders WHERE seen_by_admin = 0 AND status = 'Pending'";
$result = $conn->query($sql);
$data = $result->fetch_assoc();

echo json_encode(['new_orders' => $data['new_orders'] ?? 0]);
?>
