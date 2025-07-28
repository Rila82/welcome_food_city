<?php
session_start();
include('../config/db.php');

// ✅ Check admin login
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    die("Product ID is missing.");
}

// ✅ Fetch current product data
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    die("Product not found.");
}

// ✅ Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $brand = $_POST['brand'];
    $barcode = $_POST['barcode'];
    $category = $_POST['category'];
    $sub_category = $_POST['sub_category'];
    $capacity = $_POST['capacity'];
    $stock_qty = (int)$_POST['stock_qty'];
    $normal_price = (float)$_POST['normal_price'];
    $discount_price = (float)$_POST['discount_price'];
    $offer_start = $_POST['offer_start'] ?: null;
    $offer_end = $_POST['offer_end'] ?: null;
    $status = $_POST['status'];

    $image = $product['image'];
    if (!empty($_FILES['image']['name'])) {
        $image = basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "../assets/images/$image");
    }

    $update = $conn->prepare("UPDATE products SET 
        name=?, brand=?, barcode=?, category=?, sub_category=?, capacity=?, stock_qty=?, 
        normal_price=?, discount_price=?, offer_start=?, offer_end=?, image=?, status=? 
        WHERE id=?");

    $update->bind_param("ssssssiddsssssi", 
        $name, $brand, $barcode, $category, $sub_category, $capacity, $stock_qty,
        $normal_price, $discount_price, $offer_start, $offer_end, $image, $status, $id
    );

    if ($update->execute()) {
        header("Location: manage_products.php");
        exit;
    } else {
        $error = "Failed to update product.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .form-section {
            margin-bottom: 20px;
        }

        .card {
            max-width: 900px;
            margin: auto;
        }

        .form-control {
            font-size: 0.95rem;
        }

        .section-title {
            font-weight: 600;
            margin-bottom: 15px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
        }
    </style>
</head>
<body>

<div class="container my-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white text-center">
            <h4>Edit Product</h4>
        </div>
        <div class="card-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="row form-section">
                    <div class="col-md-6">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($product['name'] ?? '') ?>" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Brand</label>
                        <input type="text" name="brand" value="<?= htmlspecialchars($product['brand'] ?? '') ?>" class="form-control">
                    </div>
                </div>

                <div class="row form-section">
                    <div class="col-md-6">
                        <label class="form-label">Barcode</label>
                        <input type="text" name="barcode" value="<?= htmlspecialchars($product['barcode'] ?? '') ?>" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Capacity (g/ml/inch)</label>
                        <input type="text" name="capacity" value="<?= htmlspecialchars($product['capacity'] ?? '') ?>" class="form-control">
                    </div>
                </div>

                <div class="row form-section">
                    <div class="col-md-6">
                        <label class="form-label">Category</label>
                        <input type="text" name="category" value="<?= htmlspecialchars($product['category'] ?? '') ?>" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Sub Category</label>
                        <input type="text" name="sub_category" value="<?= htmlspecialchars($product['sub_category'] ?? '') ?>" class="form-control">
                    </div>
                </div>

                <div class="row form-section">
                    <div class="col-md-4">
                        <label class="form-label">Stock Quantity</label>
                        <input type="number" name="stock_qty" value="<?= (int)($product['stock_qty'] ?? 0) ?>" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Normal Price (Rs.)</label>
                        <input type="number" name="normal_price" value="<?= (float)($product['normal_price'] ?? 0.00) ?>" step="0.01" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Discount Price (Rs.)</label>
                        <input type="number" name="discount_price" value="<?= (float)($product['discount_price'] ?? 0.00) ?>" step="0.01" class="form-control">
                    </div>
                </div>

                <div class="row form-section">
                    <div class="col-md-6">
                        <label class="form-label">Offer Start</label>
                        <input type="date" name="offer_start" value="<?= $product['offer_start'] ?? '' ?>" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Offer End</label>
                        <input type="date" name="offer_end" value="<?= $product['offer_end'] ?? '' ?>" class="form-control">
                    </div>
                </div>

                <div class="form-section">
                    <label class="form-label">Current Image</label><br>
                    <?php if (!empty($product['image'])): ?>
                        <img src="../assets/images/<?= htmlspecialchars($product['image']) ?>" height="80" class="mb-2">
                    <?php endif; ?>
                    <input type="file" name="image" class="form-control">
                </div>

                <div class="form-section">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="active" <?= ($product['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="hold" <?= ($product['status'] ?? '') === 'hold' ? 'selected' : '' ?>>Hold</option>
                    </select>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-success px-4">Update Product</button>
                    <a href="manage_products.php" class="btn btn-secondary ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>
