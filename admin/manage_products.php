<?php
session_start();
include('../config/db.php');

// Check admin session
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

$result = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Products</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .product-image {
      height: 50px;
      object-fit: contain;
    }
    .table-responsive {
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
      border-radius: 10px;
      overflow: hidden;
    }
    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }
    .action-btns a {
      margin-right: 5px;
    }
  </style>
</head>
<body>

<div class="container mt-5 mb-5">
  <div class="page-header mb-3">
    <h2>📦 Manage Products</h2>
    <div>
      <a href="upload_product.php" class="btn btn-success me-2 mb-2"><i class="bi bi-plus-circle"></i> Add New Product</a>
      <a href="export_orders.php" class="btn btn-outline-primary mb-2">
        <i class="bi bi-file-earmark-excel"></i> Export Excel
      </a>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-bordered table-striped align-middle mb-0">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Image</th>
          <th>Name</th>
          <th>Brand</th>
          <th>Category</th>
          <th>Stock</th>
          <th>Price (Rs.)</th>
          <th>Offer</th>
          <th>Status</th>
          <th style="width: 160px;">Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td>
            <?php if (!empty($row['image'])): ?>
              <img src="../assets/images/<?= htmlspecialchars($row['image']) ?>" class="product-image">
            <?php else: ?>
              <span class="text-muted">No Image</span>
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($row['name'] ?? '-') ?></td>
          <td><?= htmlspecialchars($row['brand'] ?? '-') ?></td>
          <td><?= htmlspecialchars($row['category'] ?? '-') ?></td>
          <td><?= $row['stock_qty'] ?? 0 ?></td>
          <td>
            <s><?= number_format($row['normal_price'] ?? 0, 2) ?></s><br>
            <strong><?= number_format($row['discount_price'] ?? 0, 2) ?></strong>
          </td>
          <td>
            <?php
              if (!empty($row['offer_start']) && !empty($row['offer_end'])) {
                  echo date('d M', strtotime($row['offer_start'])) . " - " . date('d M', strtotime($row['offer_end']));
              } else {
                  echo '-';
              }
            ?>
          </td>
          <td>
            <a href="toggle_status.php?id=<?= $row['id'] ?>" 
               class="btn btn-sm <?= $row['status'] === 'active' ? 'btn-success' : 'btn-warning' ?>">
              <?= ucfirst($row['status']) ?>
            </a>
          </td>
          <td class="action-btns">
            <a href="edit_product.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">
              <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="delete_product.php?id=<?= $row['id'] ?>" 
               class="btn btn-sm btn-danger"
               onclick="return confirm('Are you sure you want to delete this product?');">
              <i class="bi bi-trash"></i> Delete
            </a>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>

