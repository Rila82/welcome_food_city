<?php  
// index.php
session_start();
include('config/db.php');
include('includes/header.php');

$result = $conn->query("SELECT * FROM products WHERE status = 'active'");


?>

<!-- ✅ Hero Banner -->
<div class="p-4 p-md-5 mb-4 text-white rounded bg-success text-center shadow-sm">
  <div class="col-lg-8 mx-auto">
    <h1 class="display-5 fw-bold">Welcome to Welcome Food City</h1>
    <p class="lead">Fresh groceries. Fast delivery. 10 Branches in Batticaloa.</p>
  </div>
</div>

<!-- ✅ Centered Toast Notification -->
<div id="cart-toast" class="alert alert-success alert-dismissible fade show position-fixed top-50 start-50 translate-middle shadow-lg text-center p-4 fs-5 rounded-4" style="display:none; z-index:1055; min-width: 300px; max-width: 500px;" role="alert">
  <strong id="toast-message">Item added to cart!</strong>
  <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="hideToast()"></button>
</div>

<!-- ✅ Product Section -->
<h3 class="mb-4 text-center">Our Products</h3>
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
  <?php while ($row = $result->fetch_assoc()): ?>
    <div class="col">
      <div class="card h-100 border-0 shadow-sm hover-shadow rounded-4">
        <a href="pages/product.php?id=<?= $row['id'] ?>">
          <img src="assets/images/<?= htmlspecialchars($row['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']) ?>" style="height: 200px; object-fit: contain;">
        </a>
        <div class="card-body d-flex flex-column">
          <h5 class="card-title">
            <a href="pages/product.php?id=<?= $row['id'] ?>" class="text-dark text-decoration-none">
              <?= htmlspecialchars($row['name']) ?>
            </a>
          </h5>
          <p class="card-text text-muted">Rs. <?= number_format($row['price'], 2) ?></p>
          <button class="btn btn-outline-primary mt-auto w-100 add-to-cart-btn" data-id="<?= $row['id'] ?>">Add to Cart</button>
        </div>
      </div>
    </div>
  <?php endwhile; ?>
</div>

<!-- ✅ Bootstrap JS (for alert animation) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- ✅ Toast CSS for fade-out animation -->
<style>
#cart-toast {
  opacity: 1;
  transition: opacity 0.5s ease-in-out;
}
#cart-toast.fade-out {
  opacity: 0;
}
</style>

<!-- ✅ JS: AJAX Add to Cart with Toast -->
<script>
document.addEventListener("DOMContentLoaded", function () {
  const cartCount = document.getElementById("cart-count");

  document.querySelectorAll('.add-to-cart-btn').forEach(button => {
    button.addEventListener('click', function () {
      const productId = this.getAttribute('data-id');

      fetch('pages/add_to_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + encodeURIComponent(productId)
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          showToast("✅ " + data.message);
          if (data.message.includes("added") && cartCount) {
            cartCount.textContent = parseInt(cartCount.textContent) + 1;
          }
        } else {
          showToast("❌ " + data.message);
        }
      })
      .catch(() => {
        showToast("❌ Something went wrong!");
      });
    });
  });
});

function showToast(message) {
  const toast = document.getElementById('cart-toast');
  const toastMsg = document.getElementById('toast-message');
  toastMsg.textContent = message;
  toast.classList.remove('fade-out');
  toast.style.display = 'block';

  setTimeout(() => {
    toast.classList.add('fade-out');
    setTimeout(() => {
      toast.style.display = 'none';
    }, 500);
  }, 3000);
}

function hideToast() {
  const toast = document.getElementById('cart-toast');
  toast.classList.add('fade-out');
  setTimeout(() => {
    toast.style.display = 'none';
  }, 500);
}
</script>

<?php include('includes/footer.php'); ?>
