<?php include('../includes/header.php'); ?>
<div class="container mt-5 text-center">
  <h2 class="text-success">🎉 Order Placed Successfully!</h2>
  <p>Your Order ID is: <strong>#<?= htmlspecialchars($_GET['order_id']) ?></strong></p>
  <a href="../index.php" class="btn btn-primary mt-3">Continue Shopping</a>
</div>
<?php include('../includes/footer.php'); ?>
