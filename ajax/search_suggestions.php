<?php
include('../config/db.php');

$keyword = $_GET['term'] ?? '';

if (!empty($keyword)) {
    $stmt = $conn->prepare("SELECT name FROM products WHERE name LIKE ? AND status = 'active' LIMIT 10");
    $term = "%$keyword%";
    $stmt->bind_param("s", $term);
    $stmt->execute();
    $result = $stmt->get_result();

    $suggestions = [];
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = $row['name'];
    }

    echo json_encode($suggestions);
}
?>
