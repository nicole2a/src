<?php
include '../connectie.php'; // Include the database connection

if (isset($_GET['id'])) {
    $id = (int)$_GET['id']; // Ensure the ID is an integer
    if ($id > 0) {
        // SQL query to delete the product
        $sql_delete = "DELETE FROM producten WHERE id = ?";
        $stmt = $conn->prepare($sql_delete);
        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Redirect back to the main page after deletion
header("Location: index.php");
exit();
?>
