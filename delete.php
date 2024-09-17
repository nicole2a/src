<?php
include 'connectie.php';

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Delete the product from the database
    $sql = "DELETE FROM producten WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        // Redirect back to the main page after deletion
        header("Location: index-logged-in.php");
        exit();
    } else {
        echo "Error deleting product: " . $conn->error;
    }
} else {
    die("Invalid product ID.");
}
?>
