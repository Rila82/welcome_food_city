<?php
include('../config/db.php');
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=orders_" . date("Y-m-d") . ".xls");

$result = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
echo "<table border='1'>";
echo "<tr><th>Order ID</th><th>Customer</th><th>Phone</th><th>Zone</th><th>Total</th><th>Status</th><th>Placed On</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['name']}</td>
        <td>{$row['phone']}</td>
        <td>{$row['zone']}</td>
        <td>{$row['total_amount']}</td>
        <td>{$row['status']}</td>
        <td>{$row['created_at']}</td>
    </tr>";
}
echo "</table>";
?>
