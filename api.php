<?php
header("Content-Type: application/json");
$url = "https://marcconrad.com/uob/banana/api.php";
$response = file_get_contents($url);
echo $response;
?>