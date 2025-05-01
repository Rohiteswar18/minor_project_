<?php
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id <= 0) {
    // Redirect back if no valid order ID
    header('Location: index.html');
    exit;
}

$conn = new mysqli("localhost", "root", "", "minor_project");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the specific order
$stmt = $conn->prepare("SELECT id, email, products, total_price, utr_id, created_at FROM orders WHERE id = ? AND accepted = 0");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Order not found or already accepted
    $conn->close();
    header('Location: index.html');
    exit;
}

$order = $result->fetch_assoc();
$products = json_decode($order["products"], true);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Order</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
</head>
<body>
    <div class="container py-5">
        <h1>Verify Order #<?php echo $order_id; ?></h1>
        
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Order ID: <?php echo $order["id"]; ?></h5>
                <p class="card-text"><strong>Email:</strong> <?php echo $order["email"]; ?></p>
                <p class="card-text"><strong>Date:</strong> <?php echo $order["created_at"]; ?></p>
                <p class="card-text"><strong>UTR ID:</strong> <?php echo $order["utr_id"]; ?></p>
                
                <h6>Products:</h6>
                <ul>
                <?php foreach ($products as $product): ?>
                    <li><?php echo $product["name"]; ?> - ₹<?php echo $product["price"]; ?> x <?php echo $product["quantity"]; ?> = ₹<?php echo $product["total"]; ?></li>
                <?php endforeach; ?>
                </ul>
                
                <p class="card-text"><strong>Total:</strong> ₹<?php echo $order["total_price"]; ?></p>
                
                <form action="accept_order.php" method="POST" class="mt-4">
                    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                    
                    <div class="mb-3">
                        <a href="https://lens.google.com/" target="_blank" class="btn btn-info">
                            <i class="fas fa-camera"></i> Open Google Lens
                        </a>
                        <small class="form-text text-muted">Use Google Lens to scan or verify information if needed.</small>
                    </div>

                    <div class="form-group">
                        <label for="verification_code">Enter Verification Code (3-4 digits):</label>
                        <input type="text" class="form-control" id="verification_code" name="verification_code" 
                               pattern="[0-9]{3,4}" required maxlength="4">
                        <small class="form-text text-muted">Enter 3-4 digit verification code to accept this order.</small>
                    </div>
                    
                    <button type="submit" class="btn btn-success">Accept Order</button>
                    <a href="index.html" class="btn btn-secondary ml-2">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>