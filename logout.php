<?php
session_start();

// Clear all cookies
if (isset($_COOKIE['auth_token'])) {
    setcookie('auth_token', '', time() - 3600, '/', '', false, true);
}
if (isset($_COOKIE['user_token'])) {
    setcookie('user_token', '', time() - 3600, '/');
}
if (isset($_COOKIE['username'])) {
    setcookie('username', '', time() - 3600, '/');
}

// Destroy session
session_destroy();

header("Location: index.php");
exit();
?>