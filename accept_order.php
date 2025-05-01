<?php
// accept_order.php

$host = "localhost";
$db   = "minor_project";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
$verification_code = isset($_POST['verification_code']) ? $_POST['verification_code'] : '';

// Validate verification code format
if (!preg_match('/^\d{3,4}$/', $verification_code)) {
  // Invalid verification code format
  echo "<script>alert('Invalid verification code format. Please enter 3-4 digits.'); window.location.href='verify_order.php?order_id=$order_id';</script>";
  exit;
}

if ($order_id > 0) {
  // Update the 'accepted' status to 1 and store the verification code
  $stmt = $conn->prepare("UPDATE orders SET accepted = 1, code = ? WHERE id = ?");
  $stmt->bind_param("si", $verification_code, $order_id);
  
  if ($stmt->execute()) {
    // Redirect with success message
    echo "<script>alert('Order successfully accepted with verification code: $verification_code'); window.location.href='index.html';</script>";
  } else {
    // Error handling
    echo "<script>alert('Error accepting order: " . $conn->error . "'); window.location.href='verify_order.php?order_id=$order_id';</script>";
  }
  
  $stmt->close();
} else {
  echo "<script>alert('Invalid order ID'); window.location.href='index.html';</script>";
}

$conn->close();
?>