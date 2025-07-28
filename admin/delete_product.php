<?php
session_start();
include('../config/db.php');

// ✅ Ensure admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

// ✅ Validate product ID
$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    die("Invalid product ID.");
}

// ✅ Get product image before deletion
$stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $image = $row['image'];

    // ✅ Delete product record
    $delete = $conn->prepare("DELETE FROM products WHERE id = ?");
    $delete->bind_param("i", $id);
    if ($delete->execute()) {
        // ✅ Remove image file (optional)
        $imagePath = "../assets/images/" . $image;
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
        header("Location: manage_products.php?deleted=1");
        exit;
    } else {
        die("Failed to delete product.");
    }
} else {
    die("Product not found.");
}
?>
