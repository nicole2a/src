<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include "connectie.php";

// Function to validate and sanitize input
function validate($data){
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate email and password input
    if (isset($_POST['email']) && isset($_POST['password'])) {
        
        $email = filter_var(validate($_POST['email']), FILTER_VALIDATE_EMAIL);
        $password = validate($_POST['password']);

        if (!$email) {
            header("Location: login.php?error=Invalid email format");
            exit();
        } elseif (empty($password)) {
            header("Location: login.php?error=Password is required");
            exit();
        } else {
            // Check if the user exists in the database
            $sql = "SELECT * FROM MyGuests WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc(); // Fetch the user's data

                // Verify the provided password with the stored hash
                if (password_verify($password, $user['password_hash'])) {
                    // Password is correct, set session variables
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['id'] = $user['id'];

                    // Redirect to the logged-in page
                    header("Location: index-logged-in.php");
                    exit();
                } else {
                    header("Location: login.php?error=Invalid password");
                    exit();
                }
            } else {
                header("Location: login.php?error=User not found");
                exit();
            }
        }
    } else {
        header("Location: login.php?error=Please fill in both fields");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>
