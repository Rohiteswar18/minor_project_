<?php
session_start();
$conn = new mysqli("localhost", "root", "", "minor_project");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get email from session
if (!isset($_SESSION['email'])) {
    echo "User not logged in.";
    exit;
}

$email = $_SESSION['email'];
$data = json_decode(file_get_contents("php://input"), true);

$cart = $data['cart'];
$utrId = $data['utrId'];
$totalPrice = 0;

// Build the product details and calculate total price
$products = [];
foreach ($cart as $item) {
    $product = $item['name'];
    $price = $item['price'];
    $quantity = $item['quantity'];
    $itemTotal = $price * $quantity;
    
    // Add product to products array
    $products[] = [
        'name' => $product,
        'price' => $price,
        'quantity' => $quantity,
        'total' => $itemTotal
    ];
    
    // Add to the total price
    $totalPrice += $itemTotal;
}

// Convert products array to JSON
$productsJson = json_encode($products);

// Prepare the SQL query to insert data
$sql = "INSERT INTO orders (email, products, total_price, utr_id) 
        VALUES ('$email', '$productsJson', $totalPrice, '$utrId')";

// Execute the query
if ($conn->query($sql) === TRUE) {
    echo "Payment Successful! Order saved.";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
