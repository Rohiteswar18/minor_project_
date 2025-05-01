<?php
// Start the session to access stored email
session_start();

// Check if user is logged in
if(!isset($_SESSION['email'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root"; // Default phpMyAdmin username
$password = ""; // Default empty password
$dbname = "minor_project"; // Replace with your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get logged in user's email from session
$email = $_SESSION['email'];

// Query to fetch orders for the logged-in user
$sql = "SELECT * FROM orders WHERE email = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Orders</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        .order-card {
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .order-header {
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-bottom: 1px solid #ddd;
        }
        .order-items {
            padding: 15px;
        }
        .status-pending {
            color: #f39c12;
        }
        .status-accepted {
            color: #27ae60;
        }
        .no-orders {
            text-align: center;
            padding: 40px;
            font-size: 18px;
            color: #777;
        }
        .product-item {
            border-bottom: 1px solid #eee;
            padding: 8px 0;
        }
        .product-item:last-child {
            border-bottom: none;
        }
        .code-highlight {
            background-color: #f8f9fa;
            border-radius: 4px;
            padding: 8px 12px;
            font-family: monospace;
            font-size: 16px;
            border: 1px solid #ddd;
            display: inline-block;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
    <!-- Include your navbar here -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="https://cdn-icons-png.freepik.com/256/12709/12709809.png?uid=R185085967&ga=GA1.1.1334335634.1738134770&semt=ais_hybrid" class="food-munch-logo" />
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav ml-auto">
                    <a class="nav-link" id="navItem1" href="#wcuSection">Why Choose Us?</a>
                    <a class="nav-link" href="#exploreMenuSection" id="navItem2">Grocery</a>
                    <a class="nav-link" href="#deliveryPaymentSection" id="navItem3">Delivery & Payment</a>
                    <a class="nav-link" href="#followUsSection" id="navItem4">Follow Us</a>
                    <a class="nav-link active" href="#">My Orders<span class="sr-only">(current)</span></a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="mb-4">My Orders</h2>
        
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="order-card card">
                    <div class="order-header d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Order Date:</strong> <?php echo date('M d, Y h:i A', strtotime($row['created_at'])); ?>
                        </div>
                        <div>
                            <strong>Status:</strong> 
                            <?php if($row['accepted'] == 1): ?>
                                <span class="status-accepted">Accepted</span>
                            <?php else: ?>
                                <span class="status-pending">Pending</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="order-items">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Order ID:</strong> #<?php echo $row['id']; ?>
                            </div>
                            <div class="col-md-6">
                                <strong>Payment ID:</strong> <?php echo $row['utr_id']; ?>
                            </div>
                        </div>
                        
                        <?php if($row['accepted'] == 1 && !empty($row['code'])): ?>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <strong>Order Code:</strong> 
                                <span class="code-highlight"><?php echo $row['code']; ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <strong>Order Items:</strong>
                        <div class="mt-2">
                            <?php 
                            $products = json_decode($row['products'], true);
                            foreach($products as $product): 
                            ?>
                                <div class="product-item">
                                    <div class="row">
                                        <div class="col-md-6"><?php echo $product['name']; ?></div>
                                        <div class="col-md-2">₹<?php echo $product['price']; ?></div>
                                        <div class="col-md-2">x<?php echo $product['quantity']; ?></div>
                                        <div class="col-md-2">₹<?php echo $product['total']; ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="text-right mt-3">
                            <strong>Total Amount:</strong> ₹<?php echo $row['total_price']; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-orders">
                <p>You haven't placed any orders yet!</p>
                <a href="#exploreMenuSection" class="btn btn-primary">Start Shopping</a>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close connection
$stmt->close();
$conn->close();
?>