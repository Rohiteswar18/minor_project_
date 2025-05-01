<!-- <!DOCTYPE html>
<html lang="en">

<head>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Tektur:wght@400..900&display=swap" rel="stylesheet">
  <style>
    * {
      font-family: "Tektur", sans-serif;
    }

    body {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background: linear-gradient(to right, #6a11cb, #2575fc);
    }

    .login-container {
      background: rgba(255, 255, 255, 0.1);
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      text-align: center;
      width: 300px;
    }

    .login-container img {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      margin-bottom: 15px;
    }

    .login-container input {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border: none;
      border-radius: 5px;
      text-align: center;
    }

    .login-container button {
      width: 100%;
      padding: 10px;
      background: #2575fc;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .login-container button:hover {
      background: #1a5fcc;
    }

    .remember-forgot {
      display: flex;
      justify-content: space-between;
      font-size: 12px;
      color: white;
    }

    .error-message {
      color: #dc3545;
      font-size: 14px;
      margin-top: -5px;
      margin-bottom: 10px;
    }
  </style>
</head>

<body>
  <div class="login-container">
    <img src="https://img.freepik.com/free-vector/contact-icon-3d-vector-illustration-blue-button-with-user-profile-symbol-networking-sites-apps-cartoon-style-isolated-white-background-online-communication-digital-marketing-concept_778687-1715.jpg"
      alt="User Avatar">
    <h1>Admin's Login Details</h1>
    <form id="adminForm">
      <input type="text" id="emId" placeholder="Identification ID">
      <p id="emIdErrMsgEl" class="error-message"></p>

      <input type="password" id="pass" placeholder="Password">
      <p id="passErrMsgEl" class="error-message"></p>

      <button type="submit">LOGIN</button>
    </form>
  </div>

  <script>
    const emId = document.getElementById("emId");
    const pass = document.getElementById("pass");
    const emIdErrMsgEl = document.getElementById("emIdErrMsgEl");
    const passErrMsgEl = document.getElementById("passErrMsgEl");
    const adminForm = document.getElementById("adminForm");

    function validateForm() {
      let isValid = true;

      if (emId.value.trim() === "") {
        emIdErrMsgEl.textContent = "ID is required*";
        isValid = false;
      } else {
        emIdErrMsgEl.textContent = "";
      }

      if (pass.value.trim() === "") {
        passErrMsgEl.textContent = "Password is required*";
        isValid = false;
      } else {
        passErrMsgEl.textContent = "";
      }

      return isValid;
    }

    adminForm.addEventListener("submit", function (e) {
      e.preventDefault(); // Prevent form from submitting automatically
      if (validateForm()) {
        // Optionally validate credentials here
        window.location.href = "adminHomepage.html";
      }
    });

    emId.addEventListener("blur", () => {
      emIdErrMsgEl.textContent = emId.value.trim() === "" ? "ID is required*" : "";
    });

    pass.addEventListener("blur", () => {
      passErrMsgEl.textContent = pass.value.trim() === "" ? "Password is required*" : "";
    });
  </script>
</body>

</html> -->

<?php
// Start session for potential use later
session_start();

// Initialize variables to store form data and error messages
$emIdErr = $passErr = $loginErr = "";
$emId = $pass = "";
$isFormSubmitted = false;

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $isFormSubmitted = true;
    
    // Validate employee ID
    if (empty($_POST["emId"])) {
        $emIdErr = "ID is required*";
    } else {
        $emId = trim($_POST["emId"]);
    }
    
    // Validate password
    if (empty($_POST["pass"])) {
        $passErr = "Password is required*";
    } else {
        $pass = trim($_POST["pass"]);
    }
    
    // If both fields are filled, check credentials in database
    if (empty($emIdErr) && empty($passErr)) {
        // Database connection
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "minor_project"; // Use your actual database name
        
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        // Prepare SQL statement to prevent SQL injection
        $sql = "SELECT * FROM admin WHERE id = ? AND password = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $emId, $pass);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            // Valid login - store admin info in session
            $admin = $result->fetch_assoc();
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_name'] = $admin['name'] ?? 'Admin'; // Use name if available
            
            // Close database connection
            $stmt->close();
            $conn->close();
            
            // Redirect to admin homepage
            header("Location: adminHomepage.html");
            exit();
        } else {
            // Invalid credentials
            $loginErr = "Invalid ID or password";
            
            // Close database connection
            $stmt->close();
            $conn->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Tektur:wght@400..900&display=swap" rel="stylesheet">
  <title>Admin Login</title>
  <style>
    * {
      font-family: "Tektur", sans-serif;
    }

    body {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      background: linear-gradient(to right, #6a11cb, #2575fc);
    }

    .login-container {
      background: rgba(255, 255, 255, 0.1);
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      text-align: center;
      width: 300px;
      backdrop-filter: blur(10px);
    }

    .login-container img {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      margin-bottom: 15px;
    }

    .login-container input {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border: none;
      border-radius: 5px;
      text-align: center;
      box-sizing: border-box;
    }

    .login-container button {
      width: 100%;
      padding: 10px;
      background: #2575fc;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background 0.3s;
      font-weight: bold;
    }

    .login-container button:hover {
      background: #1a5fcc;
    }

    .remember-forgot {
      display: flex;
      justify-content: space-between;
      font-size: 12px;
      color: white;
    }

    .error-message {
      color: #ff5252;
      font-size: 14px;
      margin-top: -5px;
      margin-bottom: 10px;
      text-align: left;
      min-height: 14px;
    }
    
    .global-error {
      background-color: rgba(255, 82, 82, 0.2);
      color: #ff5252;
      padding: 8px;
      border-radius: 5px;
      margin-bottom: 15px;
      font-size: 14px;
    }
    
    h1 {
      color: white;
      margin-bottom: 20px;
    }
  </style>
</head>

<body>
  <div class="login-container">
    <img src="https://img.freepik.com/free-vector/contact-icon-3d-vector-illustration-blue-button-with-user-profile-symbol-networking-sites-apps-cartoon-style-isolated-white-background-online-communication-digital-marketing-concept_778687-1715.jpg"
      alt="User Avatar">
    <h1>Admin's Login Details</h1>
    
    <?php if (!empty($loginErr)): ?>
      <div class="global-error"><?php echo $loginErr; ?></div>
    <?php endif; ?>
    
    <form id="adminForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
      <input type="text" id="emId" name="emId" placeholder="Identification ID" value="<?php echo $emId; ?>">
      <p id="emIdErrMsgEl" class="error-message"><?php echo $emIdErr; ?></p>

      <input type="password" id="pass" name="pass" placeholder="Password">
      <p id="passErrMsgEl" class="error-message"><?php echo $passErr; ?></p>

      <button type="submit">LOGIN</button>
    </form>
  </div>

  <script>
    const emId = document.getElementById("emId");
    const pass = document.getElementById("pass");
    const emIdErrMsgEl = document.getElementById("emIdErrMsgEl");
    const passErrMsgEl = document.getElementById("passErrMsgEl");
    const adminForm = document.getElementById("adminForm");

    function validateForm() {
      let isValid = true;

      if (emId.value.trim() === "") {
        emIdErrMsgEl.textContent = "ID is required*";
        isValid = false;
      } else {
        emIdErrMsgEl.textContent = "";
      }

      if (pass.value.trim() === "") {
        passErrMsgEl.textContent = "Password is required*";
        isValid = false;
      } else {
        passErrMsgEl.textContent = "";
      }

      return isValid;
    }

    adminForm.addEventListener("submit", function (e) {
      // The form will submit only if client-side validation passes
      if (!validateForm()) {
        e.preventDefault(); // Prevent form from submitting
      }
    });

    emId.addEventListener("blur", () => {
      emIdErrMsgEl.textContent = emId.value.trim() === "" ? "ID is required*" : "";
    });

    pass.addEventListener("blur", () => {
      passErrMsgEl.textContent = pass.value.trim() === "" ? "Password is required*" : "";
    });
  </script>
</body>

</html>