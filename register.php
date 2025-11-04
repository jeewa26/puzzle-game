<?php
session_start();
include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // Check passwords match
    if ($password !== $confirm) {
        $error = "❌ Passwords do not match!";
    } else {
        // Check if username exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "⚠️ Username already taken!";
        } else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert into DB
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashedPassword);

            if ($stmt->execute()) {
                $_SESSION['username'] = $username;
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "❌ Error registering user!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - Puzzle Game</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .password-field {
      position: relative;
    }
    .toggle-password {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #666;
      font-size: 14px;
    }
    .toggle-password:hover {
      color: #000;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="card" style="max-width: 500px; margin: 50px auto;">
      <h1>🧩 Puzzle Game</h1>
      <h2>Create Account</h2>
      
      <?php if (!empty($error)) echo "<p class='error-message'>$error</p>"; ?>
      
      <form method="post" onsubmit="return validateForm()">
        <input type="text" name="username" placeholder="Choose a username" required><br><br>

        <div class="password-field">
          <input type="password" id="password" name="password" placeholder="Choose a password" required>
          <span class="toggle-password" onclick="togglePassword('password')">👁️</span>
        </div><br>

        <div class="password-field">
          <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm password" required>
          <span class="toggle-password" onclick="togglePassword('confirm_password')">👁️</span>
        </div><br>

        <button type="submit" style="width: 100%; margin-bottom: 15px;">Register</button>
      </form>
      
      <div style="text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border);">
        <p style="color: var(--text-secondary);">Already have an account? <a href="index.php">Login here</a></p>
      </div>
    </div>
  </div>

  <script>
    function togglePassword(id) {
      const field = document.getElementById(id);
      field.type = field.type === "password" ? "text" : "password";
    }

    function validateForm() {
      const pass = document.getElementById("password").value;
      const confirm = document.getElementById("confirm_password").value;

      if (pass !== confirm) {
        alert("Passwords do not match!");
        return false;
      }
      return true;
    }
  </script>
</body>
</html>
