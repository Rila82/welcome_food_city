<?php
session_start();
include('../config/db.php');

// Redirect if not logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

// Get some stats
$totalProducts = $conn->query("SELECT COUNT(*) as total FROM products")->fetch_assoc()['total'];
$totalOrders   = $conn->query("SELECT COUNT(*) as total FROM orders")->fetch_assoc()['total'];
$totalUsers    = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Welcome Food City</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <!-- AOS Animation -->
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

  <style>
    body {
      background: #f4f7fc;
    }
    .dashboard-card {
      transition: transform 0.3s ease;
      border-radius: 15px;
      border: none;
    }
    .dashboard-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .card-icon {
      font-size: 40px;
    }
    .navbar {
      background: #fff !important;
    }
    .dropdown-menu {
      width: 320px;
    }
    .dropdown-item small {
      font-size: 12px;
      color: #888;
    }
    .order-item:hover {
      background-color: #f1f1f1;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg px-4 py-3 shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="#">Admin Panel</a>

    <ul class="navbar-nav ms-auto align-items-center">
      <!-- 🔔 Notification Dropdown -->
      <li class="nav-item dropdown me-3">
        <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bi bi-bell fs-4"></i>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="newOrderCount" style="display:none;">0</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow" id="notificationDropdown">
          <li class="dropdown-header fw-bold">🆕 New Orders</li>
          <div id="orderList">
            <li class="dropdown-item text-muted">No new orders</li>
          </div>
        </ul>
      </li>

      <li class="nav-item">
        <a href="logout.php" class="btn btn-outline-danger">Logout</a>
      </li>
    </ul>
  </div>
</nav>

<!-- Dashboard Content -->
<div class="container py-5">
  <h2 class="mb-5 text-center fw-bold">👨‍💼 Admin Dashboard - Welcome Food City</h2>

  <div class="row g-4">
    <div class="col-md-3" data-aos="zoom-in">
      <div class="card dashboard-card text-bg-primary p-3 text-center">
        <div class="card-body">
          <div class="card-icon mb-2"><i class="bi bi-box-seam"></i></div>
          <h5 class="card-title">Products</h5>
          <p class="card-text fs-4"><?= $totalProducts ?> Items</p>
          <a href="manage_products.php" class="btn btn-light btn-sm">Manage</a>
        </div>
      </div>
    </div>

    <div class="col-md-3" data-aos="zoom-in" data-aos-delay="100">
      <div class="card dashboard-card text-bg-success p-3 text-center">
        <div class="card-body">
          <div class="card-icon mb-2"><i class="bi bi-receipt"></i></div>
          <h5 class="card-title">Orders</h5>
          <p class="card-text fs-4"><?= $totalOrders ?> Orders</p>
          <a href="manage_orders.php" class="btn btn-light btn-sm">View</a>
        </div>
      </div>
    </div>

    <div class="col-md-3" data-aos="zoom-in" data-aos-delay="200">
      <div class="card dashboard-card text-bg-warning p-3 text-center">
        <div class="card-body">
          <div class="card-icon mb-2"><i class="bi bi-people"></i></div>
          <h5 class="card-title">Customers</h5>
          <p class="card-text fs-4"><?= $totalUsers ?> Registered</p>
          <a href="manage_users.php" class="btn btn-light btn-sm">Manage</a>
        </div>
      </div>
    </div>

    <div class="col-md-3" data-aos="zoom-in" data-aos-delay="300">
      <div class="card dashboard-card text-bg-info p-3 text-center">
        <div class="card-body">
          <div class="card-icon mb-2"><i class="bi bi-bar-chart-line"></i></div>
          <h5 class="card-title">Sales Report</h5>
          <p class="card-text fs-5">📊 Summary</p>
          <a href="sales_summary.php" class="btn btn-light btn-sm">View</a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
AOS.init();

function checkNewOrders() {
  fetch('check_new_orders.php')
    .then(res => res.json())
    .then(data => {
      const badge = document.getElementById('newOrderCount');
      const orderList = document.getElementById('orderList');

      // Update badge
      if (data.new_orders && data.orders.length > 0) {
        badge.textContent = data.orders.length;
        badge.style.display = 'inline-block';

        // Build order list
        orderList.innerHTML = '';
        data.orders.forEach(order => {
          const item = document.createElement('li');
          item.className = 'dropdown-item order-item';
          item.innerHTML = `
            <div><strong>Order #${order.id}</strong> - Rs.${order.total}</div>
            <small>${order.customer_name} | ${order.created_at}</small>
          `;
          orderList.appendChild(item);
        });
      } else {
        badge.style.display = 'none';
        orderList.innerHTML = '<li class="dropdown-item text-muted">No new orders</li>';
      }
    });
}

document.addEventListener("DOMContentLoaded", () => {
  checkNewOrders();
  setInterval(checkNewOrders, 10000); // every 10 seconds
});
</script>

</body>
</html>
