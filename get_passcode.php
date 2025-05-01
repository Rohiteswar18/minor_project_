<?php
// get_passcode.php - This file handles database operations and returns JSON data

header('Content-Type: application/json');

// Get order ID from URL parameter
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$response = ['success' => false, 'passcode' => '', 'delivery_mobile' => ''];

if ($id <= 0) {
    $response['message'] = "No valid order ID provided";
    echo json_encode($response);
    exit;
}

// Connect to the database
$conn = new mysqli("localhost", "root", "", "minor_project");

if ($conn->connect_error) {
    $response['message'] = "Database connection failed";
    echo json_encode($response);
    exit;
}

// Get the order details first to access the products and code
// Using the correct column names from your schema
$stmt = $conn->prepare("SELECT products, code FROM orders WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $order = $result->fetch_assoc();
    $products = json_decode($order["products"], true);
    
    // If the order already has a code field, use it
    if (!empty($order["code"])) {
        $response['passcode'] = $order["code"];
        $response['success'] = true;
        $response['delivery_mobile'] = "6305698151"; // Default value
    } 
    // Otherwise, get the first product ID to fetch its code
    else if (!empty($products) && isset($products[0]['id'])) {
        $product_id = $products[0]['id'];
        
        // Fetch the code from the product table
        $stmt = $conn->prepare("SELECT code FROM product WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $product_result = $stmt->get_result();
        
        if ($product_result->num_rows > 0) {
            $product = $product_result->fetch_assoc();
            $response['passcode'] = $product['code'];
            $response['success'] = true;
            $response['delivery_mobile'] = "6305698151"; // Default value
        } else {
            $response['message'] = "Product code not found";
        }
    } else {
        $response['message'] = "No products in order";
    }
} else {
    $response['message'] = "Order not found";
}

$conn->close();
echo json_encode($response);
?>