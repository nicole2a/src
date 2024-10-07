<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tools For Ever</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index-logged-in-admin.php">Tools_for_ever</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="index-logged-in-admin.php">Voorraad</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../loguit.php">Log uit</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Voorraad en Producten</h2>

        <!-- Location Selection -->
        <form action="index-logged-in-admin.php" method="get">
            <div class="mb-3">
                <label for="locatie" class="form-label">Select Location:</label>
                <select name="locatie_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All Locations</option>
                    <?php
                    include '../connectie.php';
                    $sql_locaties = "SELECT * FROM locaties";
                    $result_locaties = $conn->query($sql_locaties);
                    if ($result_locaties->num_rows > 0) {
                        while ($row = $result_locaties->fetch_assoc()) {
                            $selected = (isset($_GET['locatie_id']) && $_GET['locatie_id'] == $row["id"]) ? 'selected' : '';
                            echo '<option value="' . htmlspecialchars($row["id"]) . '" ' . $selected . '>' . htmlspecialchars($row["locatie"]) . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
        </form>

        <!-- Form for Adding New Product -->
        <form action="index-logged-in-admin.php" method="post">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <th>Type</th>
                        <th>Fabriek</th>
                        <th>Aantal</th>
                        <th>Prijs</th>
                        <th>Afbeeldingen</th>
                        <th>Locatie</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Row for adding new product -->
                    <tr>
                        <td>New</td>
                        <td><input type="text" class="form-control" name="product" placeholder="Product naam" required></td>
                        <td><input type="text" class="form-control" name="type" placeholder="Product type"></td>
                        <td><input type="text" class="form-control" name="fabriek" placeholder="Fabriek" required></td>
                        <td><input type="number" class="form-control" name="aantal" placeholder="Aantal" required min="0"></td>
                        <td><input type="number" class="form-control" name="prijs" placeholder="Prijs" required step="0.01"></td>
                        <td><input type="text" class="form-control" name="image_url" placeholder="Image URL" required></td>
                        <td>
                            <select name="locatie_id" class="form-control" required>
                                <option value="">Select Location</option>
                                <?php
                                // Fetch locations for the add form
                                $result_locaties->data_seek(0); // Reset pointer
                                while ($row = $result_locaties->fetch_assoc()) {
                                    echo '<option value="' . htmlspecialchars($row["id"]) . '">' . htmlspecialchars($row["locatie"]) . '</option>';
                                }
                                ?>
                            </select>
                        </td>
                        <td><button type="submit" name="add_product" class="btn btn-primary">Add Product</button></td>
                    </tr>

                    <!-- Existing products from the database -->
                    <?php
                    // Handle form submission for adding a product
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
                        $product = htmlspecialchars(trim($_POST['product'] ?? ''));
                        $type = htmlspecialchars(trim($_POST['type'] ?? ''));
                        $fabriek = htmlspecialchars(trim($_POST['fabriek'] ?? ''));
                        $aantal = (int)($_POST['aantal'] ?? 0);
                        $prijs = (float)($_POST['prijs'] ?? 0);
                        $image_url = htmlspecialchars(trim($_POST['image_url'] ?? ''));
                        $locatie_id = (int)($_POST['locatie_id'] ?? 0);

                        if (!empty($product) && !empty($fabriek) && $aantal >= 0 && !empty($image_url) && $locatie_id > 0 && $prijs >= 0) {
                            $sql = "INSERT INTO producten (product, type, fabriek, aantal, prijs, image_url, locatie_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
                            $stmt = $conn->prepare($sql);
                            if ($stmt) {
                                $stmt->bind_param("sssisis", $product, $type, $fabriek, $aantal, $prijs, $image_url, $locatie_id);
                                $stmt->execute();
                                $stmt->close();
                            }
                        }
                    }

                    // Handle form submission for updating a product
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
                        $product_id = (int)($_POST['id'] ?? 0);
                        $product = htmlspecialchars(trim($_POST['product'] ?? ''));
                        $type = htmlspecialchars(trim($_POST['type'] ?? ''));
                        $fabriek = htmlspecialchars(trim($_POST['fabriek'] ?? ''));
                        $aantal = (int)($_POST['aantal'] ?? 0);
                        $prijs = (float)($_POST['prijs'] ?? 0);
                        $image_url = htmlspecialchars(trim($_POST['image_url'] ?? ''));
                        $locatie_id = (int)($_POST['locatie_id'] ?? 0);

                        if ($product_id > 0 && !empty($product) && !empty($fabriek) && $aantal >= 0 && $locatie_id > 0 && $prijs >= 0) {
                            $sql_update = "UPDATE producten SET product = ?, type = ?, fabriek = ?, aantal = ?, prijs = ?, image_url = ?, locatie_id = ? WHERE id = ?";
                            $stmt_update = $conn->prepare($sql_update);
                            if ($stmt_update) {
                                $stmt_update->bind_param("sssissii", $product, $type, $fabriek, $aantal, $prijs, $image_url, $locatie_id, $product_id);
                                $stmt_update->execute();
                                $stmt_update->close();
                            }
                        }
                    }

                    // Fetch and display existing products based on selected location
                    $locatie_id = isset($_GET['locatie_id']) ? (int)$_GET['locatie_id'] : 0;
                    $sql_select = "SELECT p.*, l.locatie AS locatie FROM producten p LEFT JOIN locaties l ON p.locatie_id = l.id" . ($locatie_id > 0 ? " WHERE p.locatie_id = $locatie_id" : "");
                    $result = $conn->query($sql_select);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr>
                                      <td>' . htmlspecialchars($row["id"] ?? '') . '</td>
                                      <td>' . htmlspecialchars($row["product"] ?? '') . '</td>
                                      <td>' . htmlspecialchars($row["type"] ?? '') . '</td>
                                      <td>' . htmlspecialchars($row["fabriek"] ?? '') . '</td>
                                      <td>' . htmlspecialchars($row["aantal"] ?? 0) . '</td>
                                      <td>' . number_format($row["prijs"] ?? 0, 2) . '</td>
                                      <td><img src="' . htmlspecialchars($row["image_url"] ?? '') . '" alt="' . htmlspecialchars($row["product"] ?? '') . '" width="100"></td>
                                      <td>' . htmlspecialchars($row["locatie"] ?? '') . '</td>
                                      <td>
                                          <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal" 
                                                  onclick="loadProductData(' . htmlspecialchars($row["id"] ?? 0) . ', \'' . addslashes(htmlspecialchars($row["product"] ?? '')) . '\', \'' . addslashes(htmlspecialchars($row["type"] ?? '')) . '\', \'' . addslashes(htmlspecialchars($row["fabriek"] ?? '')) . '\', ' . htmlspecialchars($row["aantal"] ?? 0) . ', ' . htmlspecialchars($row["prijs"] ?? 0) . ', \'' . addslashes(htmlspecialchars($row["image_url"] ?? '')) . '\', ' . htmlspecialchars($row["locatie_id"] ?? 0) . ')">Edit</button>
                                          <form action="index-logged-in-admin.php" method="post" style="display:inline;">
                                              <input type="hidden" name="delete_id" value="' . htmlspecialchars($row["id"] ?? 0) . '">
                                              <button type="submit" name="delete_product" class="btn btn-danger btn-sm">Delete</button>
                                          </form>
                                      </td>
                                  </tr>';
                        }
                    }
                    ?>
                </tbody>
            </table>
        </form>

        <!-- Handle deletion -->
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
            $delete_id = (int)($_POST['delete_id'] ?? 0);
            if ($delete_id > 0) {
                $sql_delete = "DELETE FROM producten WHERE id = ?";
                $stmt_delete = $conn->prepare($sql_delete);
                if ($stmt_delete) {
                    $stmt_delete->bind_param("i", $delete_id);
                    $stmt_delete->execute();
                    $stmt_delete->close();
                }
            }
        }
        ?>

        <!-- Edit Product Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editProductForm" action="index-logged-in-admin.php" method="post">
                            <input type="hidden" name="id" id="productId">
                            <div class="mb-3">
                                <label for="productName" class="form-label">Product Name</label>
                                <input type="text" class="form-control" name="product" id="productName" required>
                            </div>
                            <div class="mb-3">
                                <label for="productType" class="form-label">Product Type</label>
                                <input type="text" class="form-control" name="type" id="productType">
                            </div>
                            <div class="mb-3">
                                <label for="fabriek" class="form-label">Fabriek</label>
                                <input type="text" class="form-control" name="fabriek" id="fabriek" required>
                            </div>
                            <div class="mb-3">
                                <label for="aantal" class="form-label">Aantal</label>
                                <input type="number" class="form-control" name="aantal" id="aantal" required min="0">
                            </div>
                            <div class="mb-3">
                                <label for="prijs" class="form-label">Prijs</label>
                                <input type="number" class="form-control" name="prijs" id="prijs" required step="0.01">
                            </div>
                            <div class="mb-3">
                                <label for="imageUrl" class="form-label">Image URL</label>
                                <input type="text" class="form-control" name="image_url" id="imageUrl" required>
                            </div>
                            <div class="mb-3">
                                <label for="locatieId" class="form-label">Locatie</label>
                                <select name="locatie_id" class="form-control" id="locatieId" required>
                                    <option value="">Select Location</option>
                                    <?php
                                    // Reset the pointer for locaties
                                    $result_locaties->data_seek(0);
                                    while ($row = $result_locaties->fetch_assoc()) {
                                        echo '<option value="' . htmlspecialchars($row["id"] ?? 0) . '">' . htmlspecialchars($row["locatie"] ?? '') . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" name="update_product" class="btn btn-primary">Update Product</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function loadProductData(id, product, type, fabriek, aantal, prijs, image_url, locatie_id) {
            document.getElementById('productId').value = id;
            document.getElementById('productName').value = product;
            document.getElementById('productType').value = type;
            document.getElementById('fabriek').value = fabriek;
            document.getElementById('aantal').value = aantal;
            document.getElementById('prijs').value = prijs;
            document.getElementById('imageUrl').value = image_url;
            document.getElementById('locatieId').value = locatie_id;
        }
    </script>
</body>
</html>
