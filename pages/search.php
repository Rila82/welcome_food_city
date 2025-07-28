<?php
include('../config/db.php');
include('../includes/header.php');

$q = $_GET['q'] ?? '';

$stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE ? AND status = 'active'");
$searchTerm = "%$q%";
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mb-5">
  <h2 class="mb-4">Search Results for "<span class="text-primary"><?= htmlspecialchars($q) ?></span>"</h2>

  <?php if ($result->num_rows > 0): ?>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="col">
          <div class="card h-100 border-0 shadow-sm hover-shadow rounded-4">
            <img src="../assets/images/<?= htmlspecialchars($row['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']) ?>" style="height: 200px; object-fit: contain;">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
              <p class="card-text text-muted">Rs. <?= number_format($row['price'], 2) ?></p>
              <a href="product.php?id=<?= $row['id'] ?>" class="btn btn-success mt-auto w-100">View Product</a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <div class="alert alert-warning text-center" role="alert">
      <strong>Item Not Found!</strong> No products match your search "<em><?= htmlspecialchars($q) ?></em>".
    </div>
  <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>
