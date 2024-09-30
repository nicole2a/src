<!-- edit.php -->
<?php
include 'connectie.php'; // Make sure this path is correct

// Check if product ID is passed
if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);

    // Fetch the product details
    $sql = "SELECT * FROM producten WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $product = $result->fetch_assoc();
    } else {
        echo "<div class='alert alert-danger'>Product not found.</div>";
        exit;
    }
    $stmt->close();
} else {
    echo "<div class='alert alert-danger'>No product ID provided.</div>";
    exit;
}

// Handle the form submission to update the product
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product = htmlspecialchars(trim($_POST['product']));
    $type = htmlspecialchars(trim($_POST['type']));
    $fabriek = htmlspecialchars(trim($_POST['fabriek']));

    if (!empty($product) && !empty($type) && !empty($fabriek)) {
        $sql_update = "UPDATE producten SET product = ?, type = ?, fabriek = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssi", $product, $type, $fabriek, $product_id);

        if ($stmt_update->execute()) {
            echo "<div class='alert alert-success'>Product updated successfully.</div>";
            header('Location: bestellen.php'); // Redirect back to product list
            exit;
        } else {
            echo "<div class='alert alert-danger'>Error updating product: " . $stmt_update->error . "</div>";
        }
        $stmt_update->close();
    } else {
        echo "<div class='alert alert-warning'>Please fill in all fields.</div>";
    }
}

$conn->close(); // Close the database connection
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Edit Product</h2>
    <form action="edit.php?id=<?= $product_id ?>" method="post">
        <div class="mb-3">
            <label for="product" class="form-label">Product Name</label>
            <input type="text" class="form-control" id="product" name="product" value="<?= $product['product'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="type" class="form-label">Product Type</label>
            <input type="text" class="form-control" id="type" name="type" value="<?= $product['type'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="fabriek" class="form-label">Fabriek</label>
            <input type="text" class="form-control" id="fabriek" name="fabriek" value="<?= $product['fabriek'] ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Product</button>
        <a href="bestellen.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
