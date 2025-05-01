<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "minor_project";

// Create DB connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get POST data
$name = $_POST['name'];
$email = $_POST['email'];
$raw_password = $_POST['password'];
$mobile = $_POST['number'];

// Hash the password
$hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);

// Check if email already exists
$checkQuery = "SELECT * FROM user WHERE Email = '$email'";
$result = $conn->query($checkQuery);

if ($result->num_rows > 0) {
    echo "<script>alert('Email already exists. Please use another email.'); window.history.back();</script>";
    exit;
}

// Insert user into DB
$sql = "INSERT INTO user (UserName, Email, Password, Mobile) 
        VALUES ('$name', '$email', '$hashed_password', '$mobile')";

if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Signup successful! Please login.'); window.location.href = 'index.html';</script>";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
