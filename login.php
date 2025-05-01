<?php
$conn = mysqli_connect("localhost", "root", "", "minor_project");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$email = $_POST['email'];
$password = $_POST['password'];

// Step 1: Fetch user by email
$sql = "SELECT * FROM user WHERE Email = '$email'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);

    // Step 2: Verify the password against the hashed password
    if (password_verify($password, $row['Password'])) {
        session_start();
$_SESSION['email'] = $email;
        echo "<script>alert('Login successful'); window.location.href='Homepage.html';</script>";
    } else {
        echo "<script>alert('Invalid password'); window.location.href='index.html';</script>";
    }
} else {
    echo "<script>alert('Email not found'); window.location.href='index.html';</script>";
}

mysqli_close($conn);
?>
