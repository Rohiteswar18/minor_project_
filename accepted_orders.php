<?php
$conn = new mysqli("localhost", "root", "", "minor_project");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Modified query to only select orders where accepted=0
$sql = "SELECT id, email, products, total_price, utr_id, created_at FROM orders WHERE accepted=1 ORDER BY created_at DESC";
$result = $conn->query($sql);

$orders = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = [
            "id" => $row["id"],
            "email" => $row["email"],
            "products" => json_decode($row["products"], true),
            "total_price" => $row["total_price"],
            "utr_id" => $row["utr_id"],
            "timestamp" => $row["created_at"]
        ];
    }
}

echo json_encode($orders);
$conn->close();
?>