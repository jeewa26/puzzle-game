<?php
session_start();
include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $avatar = $_POST['avatar'] ?? 'avataaars';

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
            $stmt = $conn->prepare("INSERT INTO users (username, password, avatar) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashedPassword, $avatar);

            if ($stmt->execute()) {
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
                
                $stmt->close();
                $conn->close();
                header("Location: index.php");
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
  <title>Register - Math Sprint</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="avatar-style.css">
</head>
<body>
  <div class="container">
    <div class="card" style="max-width: 500px; margin: 50px auto;">
      <h1>🧩 Math Sprint</h1>
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

        <div class="avatar-selection">
          <h3>👤 Choose Your Avatar</h3>
          <div class="avatar-preview">
            <img id="avatarPreview" src="https://api.dicebear.com/7.x/avataaars/svg?seed=default" alt="Avatar Preview">
          </div>
          <div class="avatar-options">
            <div class="avatar-option selected" onclick="selectAvatar('avataaars', this)">
              <input type="radio" name="avatar" value="avataaars" checked>
              <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=avataaars" alt="Avataaars">
              <label>Avataaars</label>
            </div>
            <div class="avatar-option" onclick="selectAvatar('adventurer', this)">
              <input type="radio" name="avatar" value="adventurer">
              <img src="https://api.dicebear.com/7.x/adventurer/svg?seed=adventurer" alt="Adventurer">
              <label>Adventurer</label>
            </div>
            <div class="avatar-option" onclick="selectAvatar('big-smile', this)">
              <input type="radio" name="avatar" value="big-smile">
              <img src="https://api.dicebear.com/7.x/big-smile/svg?seed=bigsmile" alt="Big Smile">
              <label>Big Smile</label>
            </div>
            <div class="avatar-option" onclick="selectAvatar('bottts', this)">
              <input type="radio" name="avatar" value="bottts">
              <img src="https://api.dicebear.com/7.x/bottts/svg?seed=bottts" alt="Bottts">
              <label>Bottts</label>
            </div>
            <div class="avatar-option" onclick="selectAvatar('fun-emoji', this)">
              <input type="radio" name="avatar" value="fun-emoji">
              <img src="https://api.dicebear.com/7.x/fun-emoji/svg?seed=funemoji" alt="Fun Emoji">
              <label>Fun Emoji</label>
            </div>
            <div class="avatar-option" onclick="selectAvatar('lorelei', this)">
              <input type="radio" name="avatar" value="lorelei">
              <img src="https://api.dicebear.com/7.x/lorelei/svg?seed=lorelei" alt="Lorelei">
              <label>Lorelei</label>
            </div>
          </div>
        </div>

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

    function selectAvatar(style, element) {
      // Remove selected class from all options
      document.querySelectorAll('.avatar-option').forEach(opt => {
        opt.classList.remove('selected');
        opt.querySelector('input[type="radio"]').checked = false;
      });
      
      // Add selected class to clicked option
      element.classList.add('selected');
      element.querySelector('input[type="radio"]').checked = true;
      
      // Update preview with username as seed
      const username = document.querySelector('input[name="username"]').value || 'default';
      const seed = username.trim() || 'default';
      document.getElementById('avatarPreview').src = `https://api.dicebear.com/7.x/${style}/svg?seed=${encodeURIComponent(seed)}`;
    }

    // Update avatar preview when username changes
    document.addEventListener('DOMContentLoaded', function() {
      const usernameInput = document.querySelector('input[name="username"]');
      usernameInput.addEventListener('input', function() {
        const selectedAvatar = document.querySelector('input[name="avatar"]:checked').value;
        const seed = this.value.trim() || 'default';
        document.getElementById('avatarPreview').src = `https://api.dicebear.com/7.x/${selectedAvatar}/svg?seed=${encodeURIComponent(seed)}`;
      });
    });

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