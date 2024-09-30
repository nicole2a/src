<?php
include 'connectie.php';

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Start transaction to ensure everything is deleted atomically
    $conn->begin_transaction();

    try {
        // Delete from producten_has_locaties (many-to-many relationship)
        $sql = "DELETE FROM producten_has_locaties WHERE producten_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();

        // Delete from voorraad_has_producten (many-to-many relationship)
        $sql = "DELETE FROM voorraad_has_producten WHERE producten_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();

        // Delete from producten table (the main table)
        $sql = "DELETE FROM producten WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();

        // Commit the transaction after successful deletion
        $conn->commit();

        // Redirect back to the main page after deletion
        header("Location: bestellen.php");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction if there's any error
        $conn->rollback();
        echo "Error deleting product: " . $e->getMessage();
    }
} else {
    die("Invalid product ID.");
}
?>
                                                                                                                                                                                                                                                                                                                                                                        