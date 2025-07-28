<?php
$host = 'localhost';
$user = 'root';
$pass = '200113302248Rila';
$dbname = 'welcome_food_city';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
