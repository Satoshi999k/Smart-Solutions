<?php
session_start();

$conn = new mysqli("localhost", "root", "", "smartsolutions");

echo "<h2>SESSION CART CONTENTS:</h2>";
echo "<pre>";
if (isset($_SESSION['cart'])) {
    print_r($_SESSION['cart']);
} else {
    echo "Empty session cart";
}
echo "</pre>";

echo "<h2>PRODUCT ID CHECK:</h2>";
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $id = $item['id'];
        $result = $conn->query("SELECT id, name, category FROM products WHERE id = $id");
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo "ID $id: {$row['name']} (Category: {$row['category']})<br>";
        } else {
            echo "ID $id: NOT FOUND IN DATABASE<br>";
        }
    }
}

$conn->close();
?>
