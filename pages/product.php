<?php
session_start();
include('../config/db.php');
include('../includes/header.php');

$product_id = $_GET['id'] ?? null;

if (!$product_id) {
  echo "<div class='container mt-5'><div class='alert alert-danger'>Invalid product ID.</div></div>";
  include('../includes/footer.php');
  exit;
}

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND status = 'active'");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
  echo "<div class='container mt-5'><div class='alert alert-warning'>Product not found or not available.</div></div>";
  include('../includes/footer.php');
  exit;
}

// Calculate discount
$now = date('Y-m-d');
$hasOffer = !empty($product['offer_percentage']) &&
            !empty($product['offer_start']) &&
            !empty($product['offer_end']) &&
            $now >= $product['offer_start'] &&
            $now <= $product['offer_end'];

$discountedPrice = $hasOffer
  ? $product['price'] - ($product['price'] * $product['offer_percentage'] / 100)
  : $product['price'];
?>

<div class="container mt-5 mb-5">
  <div class="row g-4">
    <div class="col-md-5">
      <img src="../assets/images/<?= htmlspecialchars($product['image']) ?>" class="img-fluid rounded border" alt="<?= htmlspecialchars($product['name']) ?>">
    </div>

    <div class="col-md-7">
      <h2><?= htmlspecialchars($product['name']) ?></h2>

      <?php if ($hasOffer): ?>
        <h4 class="text-success">
          Rs. <?= number_format($discountedPrice, 2) ?>
          <small class="text-muted"><del>Rs. <?= number_format($product['price'], 2) ?></del></small>
        </h4>
        <p class="text-danger">Offer: <?= $product['offer_percentage'] ?>% off (Until <?= $product['offer_end'] ?>)</p>
      <?php else: ?>
        <h4 class="text-success">Rs. <?= number_format($product['price'], 2) ?></h4>
      <?php endif; ?>

      <ul class="list-group list-group-flush mt-3 mb-3">
        <li class="list-group-item"><strong>Brand:</strong> <?= htmlspecialchars($product['brand'] ?? '-') ?></li>
        <li class="list-group-item"><strong>Barcode:</strong> <?= htmlspecialchars($product['barcode'] ?? '-') ?></li>
        <li class="list-group-item"><strong>Category:</strong> <?= htmlspecialchars($product['category'] ?? '-') ?></li>
        <li class="list-group-item"><strong>Sub Category:</strong> <?= htmlspecialchars($product['sub_category'] ?? '-') ?></li>
        <li class="list-group-item"><strong>Size:</strong> <?= htmlspecialchars($product['capacity'] ?? '-') ?></li>
        <li class="list-group-item"><strong>Stock:</strong> <?= $product['stock_qty'] ?> items available</li>
      </ul>

      <?php if (!empty($product['description'])): ?>
        <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
      <?php endif; ?>

      <?php if ($product['stock_qty'] > 0): ?>
        <button class="btn btn-primary btn-lg mt-2 add-to-cart-btn" data-id="<?= $product['id'] ?>">Add to Cart</button>
        <div id="cart-message" class="alert alert-success mt-3 d-none text-center fw-bold"></div>
      <?php else: ?>
        <div class="alert alert-warning mt-3">❌ Out of stock</div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const addToCartBtn = document.querySelector(".add-to-cart-btn");
  const messageBox = document.getElementById("cart-message");

  if (addToCartBtn) {
    addToCartBtn.addEventListener("click", function () {
      const id = this.getAttribute("data-id");

      fetch("add_to_cart.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "id=" + encodeURIComponent(id)
      })
      .then(res => res.json())
      .then(data => {
        messageBox.textContent = data.message;
        messageBox.classList.remove("d-none");
        messageBox.classList.toggle("alert-success", data.success);
        messageBox.classList.toggle("alert-danger", !data.success);
        setTimeout(() => messageBox.classList.add("d-none"), 3000);
      })
      .catch(() => {
        messageBox.textContent = "Something went wrong!";
        messageBox.classList.remove("d-none");
        messageBox.classList.add("alert-danger");
      });
    });
  }
});
</script>

<?php include('../includes/footer.php'); ?>
