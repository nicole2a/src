<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();  // Ensure no further code is executed after the redirect
}
?>

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags and other content -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tools For Ever</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Your page content goes here -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container-fluid">
        <a class="navbar-brand" href="index-logged-in.php">Tools_for_ever</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link active" href="bestellen.php">bestellen</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="index-logged-in.php">voorraad</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="bestellingen.php">bestellingen</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="loguit.php">log uit</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <div class="container mt-4">
        <h2>Voorraad en Producten</h2>

        <!-- Form for Adding New Product -->
        <form action="index-logged-in.php" method="post">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <th>Type</th>
                        <th>Fabriek</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Row for adding new product -->
                    <tr>
                        <td>New</td>
                        <td><input type="text" class="form-control" name="product" placeholder="Product naam" required></td>
                        <td><input type="text" class="form-control" name="type" placeholder="Product type" required></td>
                        <td><input type="text" class="form-control" name="fabriek" placeholder="Fabriek" required></td>
                        <td><button type="submit" name="add_product" class="btn btn-primary">Add Product</button></td>
                    </tr>

                    <!-- Existing products from the database -->
                    <?php
                    include 'connectie.php'; // Ensure this file is in the correct path

                    // Handle form submission for adding a product
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
                        $product = htmlspecialchars(stripslashes(trim($_POST['product'])));
                        $type = htmlspecialchars(stripslashes(trim($_POST['type'])));
                        $fabriek = htmlspecialchars(stripslashes(trim($_POST['fabriek'])));

                        if (!empty($product) && !empty($type) && !empty($fabriek)) {
                            $sql = "INSERT INTO producten (product, type, fabriek) VALUES (?, ?, ?)";
                            $stmt = $conn->prepare($sql);
                            if ($stmt) {
                                $stmt->bind_param("sss", $product, $type, $fabriek);
                                $stmt->execute();
                                $stmt->close();
                            }
                        }
                    }

                    // Handle form submission for updating a product
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
                        $product_id = $_POST['id'];
                        $product = htmlspecialchars(stripslashes(trim($_POST['product'])));
                        $type = htmlspecialchars(stripslashes(trim($_POST['type'])));
                        $fabriek = htmlspecialchars(stripslashes(trim($_POST['fabriek'])));

                        if (!empty($product_id) && !empty($product) && !empty($type) && !empty($fabriek)) {
                            $sql = "UPDATE producten SET product = ?, type = ?, fabriek = ? WHERE id = ?";
                            $stmt = $conn->prepare($sql);
                            if ($stmt) {
                                $stmt->bind_param("sssi", $product, $type, $fabriek, $product_id);
                                $stmt->execute();
                                $stmt->close();
                            }
                        }
                    }

                    // Fetch and display existing products
                    $sql_select = "SELECT * FROM producten";
                    $result = $conn->query($sql_select);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr>
                                      <td>' . $row["id"] . '</td>
                                      <td>' . $row["product"] . '</td>
                                      <td>' . $row["type"] . '</td>
                                      <td>' . $row["fabriek"] . '</td>
                                      <td>
                                          <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal" 
                                              onclick="loadProductData(' . $row["id"] . ', \'' . $row["product"] . '\', \'' . $row["type"] . '\', \'' . $row["fabriek"] . '\')">Edit</button>
                                          <a href="delete.php?id=' . $row["id"] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this product?\')">Delete</a>
                                      </td>
                                  </tr>';
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center'>No products found.</td></tr>";
                    }

                    $conn->close();
                    ?>
                </tbody>
            </table>
        </form>

        <!-- Edit Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="index-logged-in.php" method="post" id="editForm">
                            <input type="hidden" name="id" id="editProductId">
                            <div class="mb-3">
                                <label for="editProduct" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="editProduct" name="product" required>
                            </div>
                            <div class="mb-3">
                                <label for="editType" class="form-label">Product Type</label>
                                <input type="text" class="form-control" id="editType" name="type" required>
                            </div>
                            <div class="mb-3">
                                <label for="editFabriek" class="form-label">Fabriek</label>
                                <input type="text" class="form-control" id="editFabriek" name="fabriek" required>
                            </div>
                            <button type="submit" name="update_product" class="btn btn-primary">Update Product</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Load product data into the modal -->
    <script>
        function loadProductData(id, product, type, fabriek) {
            document.getElementById('editProductId').value = id;
            document.getElementById('editProduct').value = product;
            document.getElementById('editType').value = type;
            document.getElementById('editFabriek').value = fabriek;
        }
    </script>

    <!-- Bootstrap JS and other dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

</body>
</html>
