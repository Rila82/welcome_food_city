<?php
include('../config/db.php');
include('../includes/header.php');

// Fetch products from DB
$result = $conn->query("SELECT * FROM products");
?>

<h2 class="mb-4">Shop Products</h2>

<div class="row">
<?php while ($row = $result->fetch_assoc()): ?>
  <div class="col-md-3 mb-4">
    <div class="card h-100 shadow-sm">
      <img src="../assets/images/<?= htmlspecialchars($row['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']) ?>" style="height:200px; object-fit:contain;">
      <div class="card-body">
        <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
        <p class="card-text">Rs. <?= number_format($row['price'], 2) ?></p>
        <a href="product.php?id=<?= $row['id'] ?>" class="btn btn-primary w-100">View</a>
      </div>
    </div>
  </div>
<?php endwhile; ?>
</div>

<?php include('../includes/footer.php'); ?>
