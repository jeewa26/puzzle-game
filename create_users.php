<?php
include("db.php");

$users = [
    ["student1", "pass123"],
    ["student2", "1234"]
];

foreach ($users as $u) {
    $username = $u[0];
    $password = password_hash($u[1], PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
    if ($conn->query($sql)) {
        echo "User $username created successfully<br>";
    } else {
        echo "Error creating $username: " . $conn->error . "<br>";
    }
}

$conn->close();
?>