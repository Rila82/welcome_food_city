<?php
session_start();
include('../includes/header.php');
include('../config/db.php');

$cart = $_SESSION['cart'] ?? [];
?>

<div class="container mb-5 mt-4">
  <h2 class="mb-4">Your Shopping Cart</h2>

  <?php if (!empty($cart)): ?>
    <table class="table table-bordered table-striped align-middle">
      <thead class="table-dark">
        <tr>
          <th>Product</th>
          <th>Image</th>
          <th>Price</th>
          <th style="width: 160px;">Qty</th>
          <th>Total</th>
          <th>Remove</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $grandTotal = 0;

        foreach ($cart as $id => $item):
          $name = htmlspecialchars($item['name'] ?? 'Unknown');
          $price = $item['price'] ?? 0;
          $qty = $item['quantity'] ?? 1;
          $image = htmlspecialchars($item['image'] ?? 'default.png');

          // Fetch full product info (to check stock and status)
          $stmt = $conn->prepare("SELECT stock_qty, status FROM products WHERE id = ?");
          $stmt->bind_param("i", $id);
          $stmt->execute();
          $result = $stmt->get_result();
          $product = $result->fetch_assoc();

          // If product not found or is on hold
          if (!$product || $product['status'] !== 'active'):
        ?>
            <tr class="table-secondary text-muted" style="opacity: 0.6;">
              <td colspan="6">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <strong><?= $name ?></strong><br>
                    <em>This product is currently unavailable or on hold.</em>
                  </div>
                  <a href="remove_from_cart.php?id=<?= $id ?>" class="btn btn-sm btn-danger">Remove</a>
                </div>
              </td>
            </tr>
        <?php
            continue;
          endif;

          $stockQty = $product['stock_qty'];
          $total = $price * $qty;
          $grandTotal += $total;
        ?>
          <tr>
            <td><?= $name ?></td>
            <td><img src="../assets/images/<?= $image ?>" style="height: 60px;"></td>
            <td>Rs. <?= number_format($price, 2) ?></td>

            <td>
              <div class="input-group">
                <a href="update_cart.php?action=decrease&id=<?= $id ?>" 
                   class="btn btn-outline-secondary btn-sm <?= $qty <= 1 ? 'disabled' : '' ?>">−</a>

                <input type="text" class="form-control text-center" value="<?= $qty ?>" readonly>

                <a href="update_cart.php?action=increase&id=<?= $id ?>" 
                   class="btn btn-outline-secondary btn-sm <?= $qty >= $stockQty ? 'disabled' : '' ?>">+</a>
              </div>
              <small class="text-muted">In stock: <?= $stockQty ?></small>
            </td>

            <td>Rs. <?= number_format($total, 2) ?></td>
            <td><a href="remove_from_cart.php?id=<?= $id ?>" class="btn btn-sm btn-danger">X</a></td>
          </tr>
        <?php endforeach; ?>

        <tr>
          <td colspan="4" class="text-end"><strong>Grand Total:</strong></td>
          <td colspan="2"><strong>Rs. <?= number_format($grandTotal, 2) ?></strong></td>
        </tr>
      </tbody>
    </table>
    <div class="text-end">
      <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
    </div>
  <?php else: ?>
    <div class="alert alert-info text-center">Your cart is empty. <a href="../index.php">Go Shopping</a></div>
  <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>
