<?php
session_start();
include("db.php");

if (!isset($_SESSION['username'])) exit("Not logged in");

$username = $_SESSION['username'];
$score = $_POST['score'] ?? 0;
$difficulty = $_POST['difficulty'] ?? "easy";

$stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$user_id = $user['id'];

$stmt = $conn->prepare("INSERT INTO scores (user_id, difficulty, score) VALUES (?, ?, ?)");
$stmt->bind_param("isi", $user_id, $difficulty, $score);
$stmt->execute();

echo "Score saved!";
?>