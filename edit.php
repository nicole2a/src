<?php
include 'connectie.php';

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Get the product details by ID
    $sql = "SELECT * FROM producten WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if (!$product) {
        die("Product not found.");
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $product_name = $_POST['product_name'];
        $product_type = $_POST['product_type'];
        $voorraad = $_POST['voorraad'];
        $locatie = $_POST['locatie'];
        $aantal_in_locatie = $_POST['aantal_in_locatie'];

        // Update the product
        $update_sql = "UPDATE producten SET product = ?, type = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssi", $product_name, $product_type, $product_id);

        if ($stmt->execute()) {
            // Redirect back to the main page after successful update
            header("Location: index-logged-in.php");
            exit();
        } else {
            echo "Error updating product: " . $conn->error;
        }
    }
} else {
    die("Invalid product ID.");
}
?>

<!-- Form to edit product details -->
<form method="post" action="edit.php?id=<?php echo $product_id; ?>">
    Product Name: <input type="text" name="product_name" value="<?php echo $product['product']; ?>" required><br>
    Product Type: <input type="text" name="product_type" value="<?php echo $product['type']; ?>" required><br>
    voorraad: <input type="text" name="voorraad" value="<?php echo $product['voorraad']; ?>" required><br>
    locatie: <input type="text" name="locatie" value="<?php echo $product['locatie']; ?>" required><br>
    aantal_in_locatie: <input type="text" name="aantal_in_locatie" value="<?php echo $product['aantal_in_locatie']; ?>" required><br>
    <!-- Add fields for voorraad and locatie as needed -->
    <input type="submit" value="Update Product">
</form>

