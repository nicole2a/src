<?php
require_once 'connectie.php';

$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password_hash = $_POST['password']; 

// Password Hashing (ESSENTIAL)
$password_hash = password_hash($password_hash, PASSWORD_DEFAULT);

$mysqli = require __DIR__ . "/connectie.php";

if (!($mysqli instanceof mysqli)) {
    die("Database connection failed");
}

$sql = "INSERT INTO MyGuests (`name`, email, password_hash) VALUES (?, ?, ?)";

$stmt = $mysqli->stmt_init();

if (!$stmt->prepare($sql)) {
    die("SQL error: " . $mysqli->error);
}

$stmt->bind_param("sss", $_POST["name"], $_POST["email"], $password_hash);
if ($stmt->execute()) {
    // Redirect to the success page if execution is successful
    header("Location: signup-success.html");
    exit;
} else {
    if ($mysqli->errno === 1062) {
        // Handle duplicate entry error (e.g., email already exists)
        header("Location: signup.php?error=email_taken");
        exit;
    } else {
        // Log the error for debugging (log file or error monitoring)
        error_log("Database error: " . $mysqli->error . " (Error code: " . $mysqli->errno . ")");
        
        // Redirect to an error page or handle it in a user-friendly manner
        header("Location: signup.php?error=database_error");
        exit;
    }
    $stmt->close();
    $conn->close(); 
}
?>