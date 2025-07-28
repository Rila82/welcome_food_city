<?php
session_start();
include('../config/db.php');

$id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? null;

if (!isset($_SESSION['cart'][$id])) {
  header("Location: cart.php");
  exit;
}

if ($action === 'increase') {
  // Check stock
  $stmt = $conn->prepare("SELECT stock_qty FROM products WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stockQty = $stmt->get_result()->fetch_assoc()['stock_qty'] ?? 1;

  if ($_SESSION['cart'][$id]['quantity'] < $stockQty) {
    $_SESSION['cart'][$id]['quantity']++;
  }
} elseif ($action === 'decrease') {
  if ($_SESSION['cart'][$id]['quantity'] > 1) {
    $_SESSION['cart'][$id]['quantity']--;
  }
}

header("Location: cart.php");
exit;
