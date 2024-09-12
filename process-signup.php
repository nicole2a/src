<?php
session_start();
include "connectie.php"; // Ensure this path is correct

// Function to validate and sanitize input
function validate($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Check if form data is set
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['password_confirmation'])) {
        
        $name = validate($_POST['name']);
        $email = filter_var(validate($_POST['email']), FILTER_VALIDATE_EMAIL); // Validate email format
        $password = validate($_POST['password']);
        $password_confirmation = validate($_POST['password_confirmation']);

        $errors = [];

        // Validate input
        if (empty($name)) {
            $errors[] = "Name is required";
        }

        if (!$email) {
            $errors[] = "Invalid email format";
        }

        if (empty($password) || empty($password_confirmation)) {
            $errors[] = "Password and confirmation are required";
        }

        if ($password !== $password_confirmation) {
            $errors[] = "Passwords do not match";
        }

        if (!empty($errors)) {
            $error_message = urlencode(implode(", ", $errors));
            header("Location: sign-up.php?error={$error_message}");
            exit();
        }

        // Check if email already exists
        $sql = "SELECT * FROM MyGuests WHERE email = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Database prepare failed: " . $conn->error); // Display the error if prepare fails
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            header("Location: sign-up.php?error=" . urlencode("Email is already registered"));
            $stmt->close();
            exit();
        } else {
            // Hash the password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Insert the user into the database
            $sql_insert = "INSERT INTO MyGuests (name, email, password_hash) VALUES (?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            if ($stmt_insert === false) {
                die("Database prepare failed: " . $conn->error); // Display the error if prepare fails
            }
            $stmt_insert->bind_param("sss", $name, $email, $password_hash);

            if ($stmt_insert->execute()) {
                // Set session variables and redirect to login page
                $_SESSION['email'] = $email;
                $_SESSION['name'] = $name;
                $_SESSION['id'] = $stmt_insert->insert_id;

                $stmt_insert->close();
                header("Location: login.php");
                exit();
            } else {
                die("Database error: " . $stmt_insert->error); // Display the error if execute fails
            }
        }
        $stmt->close();
    } else {
        header("Location: sign-up.php?error=" . urlencode("Please fill in all fields"));
        exit();
    }
} else {
    header("Location: sign-up.php");
    exit();
}
?>
