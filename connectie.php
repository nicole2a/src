<?php
$servername = "mysql";
$username = "root";
$password = "password";
$db = "Tools_For_Ever";

// Create connection
$conn = new mysqli($servername, $username, $password, $db);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
return $conn;
?>

