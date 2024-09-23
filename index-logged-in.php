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
            <a class="navbar-brand" href="index-logged-in.php">Tools_for_ever</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="index-logged-in.php">voorraad</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="bestellen.php">bestellen</a>
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

        <!-- Location Selection -->
        <form action="index-logged-in.php" method="get">
            <div class="mb-3">
                <label for="locatie" class="form-label">Select Location:</label>
                <select name="locatie_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All Locations</option>
                    <?php
                    include 'connectie.php';
                    $sql_locaties = "SELECT * FROM locaties";
                    $result_locaties = $conn->query($sql_locaties);
                    if ($result_locaties->num_rows > 0) {
                        while ($row = $result_locaties->fetch_assoc()) {
                            $selected = (isset($_GET['locatie_id']) && $_GET['locatie_id'] == $row["id"]) ? 'selected' : '';
                            echo '<option value="' . $row["id"] . '" ' . $selected . '>' . $row["locatie"] . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
        </form>

        <!-- Form for Adding New Product -->
        <form action="index-logged-in.php" method="post">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <th>Type</th>
                        <th>Fabriek</th>
                        <th>Aantal</th>
                        <th>Locatie</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Row for adding new product -->
                    <tr>
                        <td>New</td>
                        <td><input type="text" class="form-control" name="product" placeholder="Product naam" required></td>
                        <td><input type="text" class="form-control" name="type" placeholder="Product type" ></td>
                        <td><input type="text" class="form-control" name="fabriek" placeholder="Fabriek" required></td>
                        <td><input type="number" class="form-control" name="aantal" placeholder="Aantal" required min="0"></td>
                        <td>
                            <select name="locatie_id" class="form-control" required>
                                <option value="">Select Location</option>
                                <?php
                                // Fetch locations for the add form
                                $result_locaties->data_seek(0); // Reset pointer
                                while ($row = $result_locaties->fetch_assoc()) {
                                    echo '<option value="' . $row["id"] . '">' . $row["locatie"] . '</option>';
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
                        $product = htmlspecialchars(stripslashes(trim($_POST['product'])));
                        if($type === null) {
                            $type = " ";
                            }
                        $fabriek = htmlspecialchars(stripslashes(trim($_POST['fabriek'])));
                        $aantal = (int)$_POST['aantal'];
                        $locatie_id = $_POST['locatie_id'];

                        if (!empty($product) && !empty($type) && !empty($fabriek) && $aantal >= 0 && !empty($locatie_id)) {
                            $sql = "INSERT INTO producten (product, type, fabriek, aantal, locatie_id) VALUES (?, ?, ?, ?, ?)";
                            $stmt = $conn->prepare($sql);
                            if ($stmt) {
                                $stmt->bind_param("ssssi", $product, $type, $fabriek, $aantal, $locatie_id);
                                $stmt->execute();
                                $stmt->close();
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
                                      <td>' . $row["id"] . '</td>
                                      <td>' . $row["product"] . '</td>
                                      <td>' . $row["type"] . '</td>
                                      <td>' . $row["fabriek"] . '</td>
                                      <td>' . $row["aantal"] . '</td>
                                      <td>' . $row["locatie"] . '</td>
                                      <td>
                                          <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal" 
                                              onclick="loadProductData(' . $row["id"] . ', \'' . $row["product"] . '\', \'' . $row["type"] . '\', \'' . $row["fabriek"] . '\', ' . $row["aantal"] . ', ' . $row["locatie_id"] . ')">Edit</button>
                                          <a href="delete.php?id=' . $row["id"] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this product?\')">Delete</a>
                                      </td>
                                  </tr>';
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center'>No products found.</td></tr>";
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
                                <input type="text" class="form-control" id="editType" name="type" >
                            </div>
                            <div class="mb-3">
                                <label for="editFabriek" class="form-label">Fabriek</label>
                                <input type="text" class="form-control" id="editFabriek" name="fabriek" required>
                            </div>
                            <div class="mb-3">
                                <label for="editAantal" class="form-label">Aantal</label>
                                <input type="number" class="form-control" id="editAantal" name="aantal" required min="0>
                            </div>
                            <div class="mb-3">
                                <label for="editLocatie" class="form-label">Locatie</label>
                                <select name="locatie_id" class="form-control" id="editLocatie" required>
                                    <option value="">Select Location</option>
                                    <?php
                                    // Fetch locations for the edit form
                                    $result_locaties->data_seek(0); // Reset pointer
                                    while ($row = $result_locaties->fetch_assoc()) {
                                        echo '<option value="' . $row["id"] . '">' . $row["locatie"] . '</option>';
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

    <!-- Load product data into the modal -->
    <script>
        function loadProductData(id, product, type, fabriek, aantal, locatie_id) {
            document.getElementById('editProductId').value = id;
            document.getElementById('editProduct').value = product;
            document.getElementById('editType').value = type;
            document.getElementById('editFabriek').value = fabriek;
            document.getElementById('editAantal').value = aantal;
            document.getElementById('editLocatie').value = locatie_id;
        }
    </script>

    <!-- Bootstrap JS and other dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
