<?php
session_start();
include('../config/db.php');
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

$totalOrders = $conn->query("SELECT COUNT(*) AS count FROM orders")->fetch_assoc()['count'];
$totalSales = $conn->query("SELECT SUM(total_amount) AS total FROM orders WHERE status='Completed'")->fetch_assoc()['total'] ?? 0;
$pendingOrders = $conn->query("SELECT COUNT(*) AS count FROM orders WHERE status='Pending'")->fetch_assoc()['count'];
$cancelledOrders = $conn->query("SELECT COUNT(*) AS count FROM orders WHERE status='Cancelled'")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html>
<head>
  <title>Sales Summary</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
  <h2 class="text-center mb-4">📊 Sales Summary Dashboard</h2>
  <div class="row text-center">
    <div class="col-md-3"><div class="card text-white bg-primary mb-3"><div class="card-body"><h5>Total Orders</h5><p><?= $totalOrders ?></p></div></div></div>
    <div class="col-md-3"><div class="card text-white bg-success mb-3"><div class="card-body"><h5>Total Sales</h5><p>Rs. <?= number_format($totalSales, 2) ?></p></div></div></div>
    <div class="col-md-3"><div class="card text-white bg-warning mb-3"><div class="card-body"><h5>Pending Orders</h5><p><?= $pendingOrders ?></p></div></div></div>
    <div class="col-md-3"><div class="card text-white bg-danger mb-3"><div class="card-body"><h5>Cancelled Orders</h5><p><?= $cancelledOrders ?></p></div></div></div>
  </div>
  <div class="text-center"><a href="admin_dashboard.php" class="btn btn-secondary mt-4">← Back to Dashboard</a></div>
</div>
</body>
</html>
