<?php
include 'connectie.php';

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Get the product details along with voorraad, locatie, and aantal_in_locatie
    $sql = "
        SELECT 
            p.product, 
            p.type, 
            v.voorraad, 
            l.locatie, 
            pl.aantal AS aantal_in_locatie 
        FROM producten p 
        JOIN voorraad_has_producten vhp ON p.id = vhp.producten_id 
        JOIN voorraad v ON vhp.voorraad_id = v.id 
        JOIN producten_has_locaties pl ON p.id = pl.producten_id 
        JOIN locaties l ON pl.locaties_id = l.id
        WHERE p.id = ?
    ";
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

        // Update the product in producten table
        $update_product_sql = "UPDATE producten SET product = ?, type = ? WHERE id = ?";
        $stmt = $conn->prepare($update_product_sql);
        $stmt->bind_param("ssi", $product_name, $product_type, $product_id);
        $stmt->execute();

        // Update the voorraad
        $update_voorraad_sql = "UPDATE voorraad v JOIN voorraad_has_producten vhp ON v.id = vhp.voorraad_id SET v.voorraad = ? WHERE vhp.producten_id = ?";
        $stmt = $conn->prepare($update_voorraad_sql);
        $stmt->bind_param("ii", $voorraad, $product_id);
        $stmt->execute();

        // Update locatie and aantal_in_locatie
        $update_locatie_sql = "UPDATE locaties l JOIN producten_has_locaties pl ON l.id = pl.locaties_id SET l.locatie = ?, pl.aantal = ? WHERE pl.producten_id = ?";
        $stmt = $conn->prepare($update_locatie_sql);
        $stmt->bind_param("sii", $locatie, $aantal_in_locatie, $product_id);
        $stmt->execute();

        // Redirect after successful update
        header("Location: index-logged-in.php");
        exit();
    }
} else {
    die("Invalid product ID.");
}
?>

<!-- Form to edit product details -->
<form method="post" action="edit.php?id=<?php echo $product_id; ?>">
    Product Name: <input type="text" name="product_name" value="<?php echo $product['product']; ?>" required><br>
    Product Type: <input type="text" name="product_type" value="<?php echo $product['type']; ?>" required><br>
    Voorraad: <input type="text" name="voorraad" value="<?php echo $product['voorraad']; ?>" required><br>
    Locatie: <input type="text" name="locatie" value="<?php echo $product['locatie']; ?>" required><br>
    Aantal in Locatie: <input type="text" name="aantal_in_locatie" value="<?php echo $product['aantal_in_locatie']; ?>" required><br>
    <input type="submit" value="Update Product">
</form>
