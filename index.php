<?php
session_start();
include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];


    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $username;
            
            // Generate a secure token
            $token = bin2hex(random_bytes(32)); // 64 character hex string
            
            // Store token in session
            $_SESSION['auth_token'] = $token;
            
            // Set cookie with token (expires in 7 days)
            setcookie('auth_token', $token, time() + (7 * 24 * 60 * 60), '/', '', false, true); // HttpOnly for security
            
            // Also set a readable cookie for presentation purposes
            setcookie('user_token', $token, time() + (7 * 24 * 60 * 60), '/');
            setcookie('username', $username, time() + (7 * 24 * 60 * 60), '/');
            
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "❌ Wrong password!";
        }
    } else {
        $error = "❌ User not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Math Sprint</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <div class="card" style="max-width: 500px; margin: 50px auto;">
      <h1>🧩 Math Sprint</h1>
      <h2>Welcome Back!</h2>
      
      <?php if (!empty($error)) echo "<p class='error-message'>$error</p>"; ?>
      
      <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" style="width: 100%; margin-bottom: 15px; margin-top: 10px;">Login</button>
      </form>

      <a href="guest_game.php" style="display: inline-block; width: 100%; padding: 14px 28px; background: linear-gradient(135deg, #FFEB3B 0%, #FF8C42 100%); color: #1A1B26; text-decoration: none; border-radius: 12px; font-weight: 700; font-family: 'Fredoka', sans-serif; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 4px 16px rgba(255, 235, 59, 0.3); transition: all 0.3s ease;">
        🍌 Play as Guest (Beginner Mode)
      </a>

      <div style="text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border);">
        <p style="color: var(--text-secondary); margin-bottom: 15px;">Don't have an account?</p>
        <a href="register.php" style="text-decoration: none; display: block;">
          <button type="button" style="width: 100%; background: linear-gradient(135deg, rgba(99, 102, 241, 0.3) 0%, rgba(139, 92, 246, 0.3) 100%); border: 2px solid rgba(99, 102, 241, 0.5); box-shadow: 0 4px 15px rgba(99, 102, 241, 0.2);">
            Create Account
          </button>
        </a>
      </div>
    </div>
  </div>
</body>
</html>