<?php
$servername = "mysql";
$username = "root";
$password = "password";
$db = "Tools_For_Ever";

// Create connection
$conn = new mysqli($servername, $username, $password, $db);

$ = $mysqli->real_escape_string($_POST['']);
$ = $mysqli->real_escape_string($_POST['']);

$query = "INSERT INTO voorraad (id, voorraad)
            VALUES ('{$}','{$}')";

$mysqli->query($query);
$mysqli->close();