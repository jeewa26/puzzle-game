<?php
session_start();
include("db.php");

if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $username;
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "❌ Invalid password!";
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
  <title>Login - Puzzle Game</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <div class="card" style="max-width: 500px; margin: 50px auto;">
      <h1>🧩 Puzzle Game</h1>
      <h2>Welcome Back!</h2>
      
      <?php if (!empty($error)) echo "<p class='error-message'>" . htmlspecialchars($error) . "</p>"; ?>
      
      <form method="post">
        <input type="text" name="username" placeholder="Username" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>
        <button type="submit" style="width: 100%; margin-bottom: 15px;">Login 🚀</button>
      </form>
      
      <div style="text-align: center; margin-top: 10px;">
        <a href="dog_game.php" style="display: inline-block; width: 100%; padding: 14px 28px; background: linear-gradient(135deg, #FF8C42 0%, #FFD93D 100%); color: #1A1B26; text-decoration: none; border-radius: 12px; font-weight: 700; font-family: 'Fredoka', sans-serif; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 4px 16px rgba(255, 140, 66, 0.3); transition: all 0.3s ease;">
          🐶 Play Dog Game (No Login Required)
        </a>
      </div>
      
      <div style="text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.1);">
        <p style="color: rgba(255, 255, 255, 0.8);">Don't have an account? <a href="register.php">Register here</a></p>
      </div>
    </div>
  </div>
</body>
</html>