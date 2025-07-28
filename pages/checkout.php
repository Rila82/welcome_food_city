<?php
session_start();
include('../config/db.php');
include('../includes/header.php');

$cart = $_SESSION['cart'] ?? [];
$grandTotal = 0;

if (empty($cart)) {
  echo "<div class='container mt-5 alert alert-warning text-center'>Your cart is empty. <a href='../index.php'>Go Shopping</a></div>";
  include('../includes/footer.php');
  exit;
}

// Fetch delivery zones
$zones = $conn->query("SELECT * FROM delivery_zones ORDER BY area_name ASC");
?>

<div class="container mt-5 mb-5">
  <h2 class="mb-4">Checkout</h2>
  <div class="row">
    <!-- ✅ Customer Form -->
    <div class="col-md-6">
      <form action="place_order.php" method="POST">
        <div class="mb-3">
          <label>Customer Name</label>
          <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Phone Number</label>
          <input type="text" name="phone" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Delivery Address</label>
          <textarea name="address" class="form-control" rows="3" required></textarea>
        </div>
        <div class="mb-3">
          <label>Zone</label>
          <select name="zone_id" id="zone" class="form-select" required onchange="updateShippingFee()">
            <option value="">-- Select Zone --</option>
            <?php while ($zone = $zones->fetch_assoc()): ?>
              <option value="<?= $zone['id'] ?>" data-fee="<?= $zone['delivery_fee'] ?>" data-name="<?= htmlspecialchars($zone['area_name']) ?>">
                <?= $zone['area_name'] ?> - Rs. <?= number_format($zone['delivery_fee'], 2) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="mb-3">
          <label>Shipping Fee</label>
          <input type="text" id="shipping_fee_display" class="form-control" readonly>
          <input type="hidden" name="shipping_fee" id="shipping_fee">
          <input type="hidden" name="zone_name" id="zone_name">
        </div>
        <div class="mb-3">
          <label>Payment Method</label>
          <select name="payment_method" class="form-control" required>
            <option value="cash_on_delivery">Cash on Delivery</option>
            <option value="online_payment">Online Payment</option>
          </select>
        </div>
        <button type="submit" class="btn btn-success w-100">Place Order</button>
      </form>
    </div>

    <!-- ✅ Order Summary -->
    <div class="col-md-6">
      <h4>Order Summary</h4>
      <ul class="list-group mb-3">
        <?php foreach ($cart as $item): 
          $total = $item['price'] * $item['quantity'];
          $grandTotal += $total;
        ?>
          <li class="list-group-item d-flex justify-content-between">
            <?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?>
            <span>Rs. <?= number_format($total, 2) ?></span>
          </li>
        <?php endforeach; ?>
        <li class="list-group-item d-flex justify-content-between bg-light">
          <strong>Subtotal</strong>
          <strong>Rs. <?= number_format($grandTotal, 2) ?></strong>
        </li>
        <li class="list-group-item d-flex justify-content-between bg-light">
          <strong>Shipping Fee</strong>
          <strong id="shippingFeeText">--</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between bg-warning-subtle">
          <strong>Total (with Shipping)</strong>
          <strong id="totalWithShippingText">--</strong>
        </li>
      </ul>
    </div>
  </div>
</div>

<!-- ✅ JS to update totals dynamically -->
<script>
function updateShippingFee() {
  const zone = document.getElementById('zone');
  const selected = zone.options[zone.selectedIndex];
  const fee = parseFloat(selected.dataset.fee || 0);
  const name = selected.dataset.name || "";
  const subtotal = <?= $grandTotal ?>;

  document.getElementById('shipping_fee').value = fee;
  document.getElementById('shipping_fee_display').value = "Rs. " + fee.toFixed(2);
  document.getElementById('zone_name').value = name;

  // Update summary
  document.getElementById('shippingFeeText').innerText = "Rs. " + fee.toFixed(2);
  document.getElementById('totalWithShippingText').innerText = "Rs. " + (subtotal + fee).toFixed(2);
}
</script>

<?php include('../includes/footer.php'); ?>
