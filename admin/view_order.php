<?php
session_start();
include('../config/db.php');

// ✅ Admin access check
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

// ✅ Get Order ID
$orderId = $_GET['id'] ?? 0;
if (!$orderId || !is_numeric($orderId)) {
    echo "Invalid Order ID.";
    exit;
}

// ✅ Fetch order info
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $orderId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    echo "Order not found.";
    exit;
}

// ✅ Mark order as seen by admin
$updateSeen = $conn->prepare("UPDATE orders SET seen_by_admin = 1 WHERE id = $orderId");
$updateSeen->bind_param("i", $orderId);
$updateSeen->execute();


// ✅ Fetch order items
$stmt = $conn->prepare("
    SELECT oi.*, p.name AS product_name 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
");
$stmt->bind_param("i", $orderId);
$stmt->execute();
$orderItems = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Order #<?= $orderId ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
  <h2 class="mb-4 fw-bold text-center">📦 Order Details - #<?= $orderId ?></h2>

  <!-- ✅ Customer and Order Info -->
  <div class="row mb-4">
    <div class="col-md-6">
      <h5>Customer Info</h5>
      <p><strong>Name:</strong> <?= htmlspecialchars($order['name']) ?></p>
      <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?></p>
      <p><strong>Address:</strong> <?= nl2br(htmlspecialchars($order['address'])) ?></p>
      <p><strong>Zone:</strong> <?= htmlspecialchars($order['zone']) ?></p>
    </div>
    <div class="col-md-6">
      <h5>Order Summary</h5>
      <p><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
      <p><strong>Shipping Fee:</strong> Rs. <?= number_format($order['shipping_fee'], 2) ?></p>
      <p><strong>Total:</strong> Rs. <?= number_format($order['total_amount'], 2) ?></p>
      <p><strong>Status:</strong> 
        <span class="badge bg-info"><?= htmlspecialchars($order['status']) ?></span>
      </p>
      <p><strong>Placed On:</strong> <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></p>
      <?php if (!empty($order['completed_at'])): ?>
        <p><strong>Completed At:</strong> <?= date('d M Y, h:i A', strtotime($order['completed_at'])) ?></p>
      <?php endif; ?>
      <?php if (!empty($order['cancelled_at'])): ?>
        <p><strong>Cancelled At:</strong> <span class="text-danger"><?= date('d M Y, h:i A', strtotime($order['cancelled_at'])) ?></span></p>
      <?php endif; ?>
    </div>
  </div>

  <!-- ✅ Ordered Products -->
  <h5>Ordered Items</h5>
  <table class="table table-bordered table-striped">
    <thead class="table-dark">
      <tr>
        <th>Product</th>
        <th>Qty</th>
        <th>Price (each)</th>
        <th>Subtotal</th>
      </tr>
    </thead>
    <tbody>
      <?php $subtotal = 0; ?>
      <?php while ($item = $orderItems->fetch_assoc()): ?>
        <?php 
          $lineTotal = $item['price'] * $item['quantity'];
          $subtotal += $lineTotal;
        ?>
        <tr>
          <td><?= htmlspecialchars($item['product_name']) ?></td>
          <td><?= (int)$item['quantity'] ?></td>
          <td>Rs. <?= number_format($item['price'], 2) ?></td>
          <td>Rs. <?= number_format($lineTotal, 2) ?></td>
        </tr>
      <?php endwhile; ?>
      <tr>
        <td colspan="3" class="text-end"><strong>Shipping:</strong></td>
        <td>Rs. <?= number_format($order['shipping_fee'], 2) ?></td>
      </tr>
      <tr>
        <td colspan="3" class="text-end"><strong>Total Amount:</strong></td>
        <td><strong>Rs. <?= number_format($order['total_amount'], 2) ?></strong></td>
      </tr>
    </tbody>
  </table>

  <div class="text-center mt-4">
    <a href="manage_orders.php" class="btn btn-secondary">← Back to Order List</a>
  </div>
</div>
</body>
</html>

