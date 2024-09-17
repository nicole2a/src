<?php
include 'connectie.php'; // Ensure this file is in the correct path

// Function to validate and sanitize input
function validate($data){
    return htmlspecialchars(stripslashes(trim($data)));
}

// Check if form data is set
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product']) && isset($_POST['type']) && isset($_POST['fabriek'])) {
        
        $product = validate($_POST['product']);
        $type = validate($_POST['type']);
        $fabriek = validate($_POST['fabriek']);
        
        if (empty($product) || empty($type) || empty($fabriek)) {
            die("Please fill in all fields.");
        }

        // Insert the new product into the database
        $sql = "INSERT INTO producten (product, type, fabrieken) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Database prepare failed: " . $conn->error); // Display the error if prepare fails
        }
        $stmt->bind_param("sss", $product, $type, $fabriek);
        
        if ($stmt->execute()) {
            // Redirect to the page displaying products
            header("Location: index-logged-in.php");
            exit();
        } else {
            die("Database error: " . $stmt->error); // Display the error if execute fails
        }
    } else {
        die("Please fill in all fields.");
    }
} else {
    die("Invalid request method.");
}

$conn->close(); // Close the database connection
?>
