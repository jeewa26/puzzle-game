<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "banana_game";

$conn = new mysqli("127.0.0.1:3306", "root", "", "puzzle_game1");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>