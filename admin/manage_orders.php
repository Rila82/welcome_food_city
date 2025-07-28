<?php
session_start();
include('../config/db.php');

// ✅ Admin access check
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

// ✅ Filters
$statusFilter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';
$whereClause = "WHERE 1 ";

if ($statusFilter && in_array($statusFilter, ['Pending', 'Completed', 'Cancelled'])) {
    $whereClause .= "AND status = '" . $conn->real_escape_string($statusFilter) . "' ";
}

if (!empty($search)) {
    $escapedSearch = $conn->real_escape_string($search);
    $whereClause .= "AND (name LIKE '%$escapedSearch%' OR phone LIKE '%$escapedSearch%' OR id = '$escapedSearch') ";
}

// ✅ Fetch orders
$sql = "SELECT * FROM orders $whereClause ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Orders - Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    .status-badge {
      padding: 0.4em 0.8em;
      border-radius: 20px;
    }
  </style>
</head>
<body>

<div class="container my-5">
  <h2 class="mb-4 fw-bold text-center">🧾 Manage Orders</h2>

  <!-- 🔍 Filters -->
  <form method="GET" class="row mb-4 g-2">
    <div class="col-md-3">
      <select name="status" class="form-select">
        <option value="">All Statuses</option>
        <option value="Pending" <?= $statusFilter === 'Pending' ? 'selected' : '' ?>>Pending</option>
        <option value="Completed" <?= $statusFilter === 'Completed' ? 'selected' : '' ?>>Completed</option>
        <option value="Cancelled" <?= $statusFilter === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
      </select>
    </div>
    <div class="col-md-4">
      <input type="text" name="search" class="form-control" placeholder="Search by Name, Phone or ID" value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-primary w-100">Search</button>
    </div>
    <div class="col-md-2">
      <a href="manage_orders.php" class="btn btn-secondary w-100">Reset</a>
    </div>
  </form>

  <!-- 🧾 Orders Table -->
  <table class="table table-bordered table-hover table-striped">
    <thead class="table-dark">
      <tr>
        <th>#Order ID</th>
        <th>Customer</th>
        <th>Zone</th>
        <th>Phone</th>
        <th>Total</th>
        <th>Payment</th>
        <th>Status</th>
        <th>Placed On</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <?php
          $status = $row['status'] ?? 'Pending';
          $badgeClass = match ($status) {
            'Pending' => 'bg-warning',
            'Completed' => 'bg-success text-white',
            'Cancelled' => 'bg-danger text-white',
            default => 'bg-secondary text-white',
          };

          $isNew = ($row['seen_by_admin'] == 0 && $status === 'Pending');
          $rowClass = $isNew ? 'table-warning' : '';
        ?>
        <tr class="<?= $rowClass ?>">
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['name'] ?? '') ?></td>
          <td><?= htmlspecialchars($row['zone'] ?? '') ?></td>
          <td><?= htmlspecialchars($row['phone'] ?? '') ?></td>
          <td>Rs. <?= number_format($row['total_amount'] ?? 0, 2) ?></td>
          <td><?= htmlspecialchars($row['payment_method'] ?? '') ?></td>
          <td><span class="badge status-badge <?= $badgeClass ?>"><?= $status ?></span></td>
          <td><?= date('d M Y, h:i A', strtotime($row['created_at'] ?? '')) ?></td>
          <td>
            <a href="view_order.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info mb-1">View</a>
            <?php if ($status === 'Pending'): ?>
              <a href="update_order_status.php?id=<?= $row['id'] ?>&status=Completed" class="btn btn-sm btn-success mb-1">Complete</a>
              <a href="update_order_status.php?id=<?= $row['id'] ?>&status=Cancelled" class="btn btn-sm btn-danger mb-1">Cancel</a>
            <?php else: ?>
              <span class="text-muted small">No Actions</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="9" class="text-center text-muted">No orders found.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>

  <div class="text-center">
    <a href="admin_dashboard.php" class="btn btn-secondary mt-3">← Back to Dashboard</a>
  </div>
</div>
</body>
</html>
