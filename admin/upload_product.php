<?php
session_start();
include('../config/db.php');

// ✅ Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $barcode = $_POST['barcode'];
    $brand = $_POST['brand'];
    $category = $_POST['category'];
    $sub_category = $_POST['sub_category'];
    $capacity = $_POST['capacity'];
    $normal_price = $_POST['normal_price'];
    $discount_price = $_POST['discount_price'];
    $stock_qty = $_POST['stock_qty'];
    $offer_start = $_POST['offer_start'] ?: null;
    $offer_end = $_POST['offer_end'] ?: null;
    $status = $_POST['status'];

    // ✅ Upload Image
    $image = '';
    if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
        $target_dir = "../assets/images/";
        $image = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    }

    // ✅ Insert into DB
    $stmt = $conn->prepare("INSERT INTO products 
        (name, barcode, brand, category, sub_category, capacity, normal_price, discount_price, price, stock_qty, offer_start, offer_end, image, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("sssssssdddssss", 
        $name, $barcode, $brand, $category, $sub_category, $capacity, 
        $normal_price, $discount_price, $discount_price, $stock_qty, 
        $offer_start, $offer_end, $image, $status);

    if ($stmt->execute()) {
        $msg = "✅ Product added successfully!";
    } else {
        $msg = "❌ Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4 mb-5">
    <h3 class="mb-4">📦 Upload New Product</h3>

    <?php if (!empty($msg)): ?>
        <div class="alert alert-info"><?= $msg ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm">
        <div class="row g-3">
            <div class="col-md-6">
                <label>Product Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label>Barcode</label>
                <input type="text" name="barcode" class="form-control">
            </div>

            <div class="col-md-6">
                <label>Brand</label>
                <input type="text" name="brand" class="form-control">
            </div>

            <div class="col-md-6">
                <label>Category</label>
                <input type="text" name="category" class="form-control">
            </div>

            <div class="col-md-6">
                <label>Sub Category</label>
                <input type="text" name="sub_category" class="form-control">
            </div>

            <div class="col-md-6">
                <label>Capacity (e.g., 500g / 1L / 10cm)</label>
                <input type="text" name="capacity" class="form-control">
            </div>

            <div class="col-md-6">
                <label>Normal Price</label>
                <input type="number" name="normal_price" step="0.01" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label>Discount Price (Offer Price)</label>
                <input type="number" name="discount_price" step="0.01" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label>Stock Quantity</label>
                <input type="number" name="stock_qty" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label>Offer Start Date</label>
                <input type="date" name="offer_start" class="form-control">
            </div>

            <div class="col-md-6">
                <label>Offer End Date</label>
                <input type="date" name="offer_end" class="form-control">
            </div>

            <div class="col-md-6">
                <label>Product Status</label>
                <select name="status" class="form-control">
                    <option value="active">Active</option>
                    <option value="hold">Hold</option>
                </select>
            </div>

            <div class="col-md-6">
                <label>Product Image</label>
                <input type="file" name="image" class="form-control" required>
            </div>
        </div>

        <button type="submit" class="btn btn-success mt-4 w-100">Upload Product</button>
    </form>
</div>
</body>
</html>
