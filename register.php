<?php
session_start();
include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // Validation
    if (strlen($username) < 3) {
        $error = "❌ Username must be at least 3 characters!";
    } elseif (strlen($password) < 6) {
        $error = "❌ Password must be at least 6 characters!";
    } elseif ($password !== $confirm) {
        $error = "❌ Passwords do not match!";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "⚠️ Username already taken!";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt->close(); // Close previous statement
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashedPassword);

            if ($stmt->execute()) {
                $_SESSION['username'] = $username;
                $stmt->close();
                $conn->close();
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "❌ Error registering user!";
            }
        }
        $stmt->close();
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
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      font-size: 18px;
      user-select: none;
      z-index: 10;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="card" style="max-width: 500px; margin: 50px auto;">
      <h1>🧩 Puzzle Game</h1>
      <h2>Create Account</h2>
      
      <?php if (!empty($error)) echo "<p class='error-message'>" . htmlspecialchars($error) . "</p>"; ?>
      
      <form method="post" onsubmit="return validateForm()">
        <input type="text" name="username" placeholder="Choose a username (min 3 characters)" required minlength="3"><br><br>

        <div class="password-field">
          <input type="password" id="password" name="password" placeholder="Choose a password (min 6 characters)" required minlength="6">
          <span class="toggle-password" onclick="togglePassword('password')" title="Show/hide password">👁️</span>
        </div><br>

        <div class="password-field">
          <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm password" required minlength="6">
          <span class="toggle-password" onclick="togglePassword('confirm_password')" title="Show/hide password">👁️</span>
        </div><br>

        <button type="submit" style="width: 100%; margin-bottom: 15px;">Register 🚀</button>
      </form>
      
      <div style="text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.1);">
        <p style="color: rgba(255, 255, 255, 0.8);">Already have an account? <a href="index.php">Login here</a></p>
      </div>
    </div>
  </div>

  <script>
    function togglePassword(id) {
      const field = document.getElementById(id);
      const toggle = field.nextElementSibling;
      
      if (field.type === "password") {
        field.type = "text";
        toggle.textContent = "🙈";
      } else {
        field.type = "password";
        toggle.textContent = "👁️";
      }
    }

    function validateForm() {
      const username = document.querySelector('input[name="username"]').value;
      const pass = document.getElementById("password").value;
      const confirm = document.getElementById("confirm_password").value;

      if (username.length < 3) {
        alert("Username must be at least 3 characters!");
        return false;
      }

      if (pass.length < 6) {
        alert("Password must be at least 6 characters!");
        return false;
      }

      if (pass !== confirm) {
        alert("Passwords do not match!");
        return false;
      }
      
      return true;
    }
  </script>
</body>
</html>