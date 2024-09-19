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

    <div class="input-group mb-3">
        <div class="input-group-prepend">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Dropdown</button>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="#">Action</a>
                <a class="dropdown-item" href="#">Another action</a>
                <a class="dropdown-item" href="#">Something else here</a>
                <div role="separator" class="dropdown-divider"></div>
                <a class="dropdown-item" href="#">Separated link</a>
            </div>
        </div>
        <input type="text" class="form-control" aria-label="Text input with dropdown button">
    </div>

    <div class="container mt-4">
        <h2>Voorraad en Producten</h2>

        <!-- Form for Adding New Product -->
        <div class="mb-4">
            <h3>Add New Product</h3>
            <form action="index-logged-in.php" method="post">
                <div class="mb-3">
                    <input type="text" class="form-control" id="product" name="product" placeholder="Product naam" required>
                    <input type="text" class="form-control" id="type" name="type" placeholder="Product type" required>
                    <input type="text" class="form-control" id="fabriek" name="fabriek" placeholder="Fabriek" required>
                </div>
                <button type="submit" class="btn btn-primary">Add Product</button>
            </form>
        </div>

        <!-- Handle form submission and display success or error messages -->
        <?php
        include 'connectie.php'; // Ensure this file is in the correct path

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['product']) && isset($_POST['type']) && isset($_POST['fabriek'])) {
                $product = htmlspecialchars(stripslashes(trim($_POST['product'])));
                $type = htmlspecialchars(stripslashes(trim($_POST['type'])));
                $fabriek = htmlspecialchars(stripslashes(trim($_POST['fabriek'])));

                if (!empty($product) && !empty($type) && !empty($fabriek)) {
                    // Check if the product already exists
                    $check_sql = "SELECT * FROM producten WHERE product = ?";
                    $check_stmt = $conn->prepare($check_sql);
                    $check_stmt->bind_param("s", $product);
                    $check_stmt->execute();
                    $check_result = $check_stmt->get_result();

                    if ($check_result->num_rows > 0) {
                        echo "<div class='alert alert-warning'>Product already exists.</div>";
                    } else {
                        // Insert into `producten`
                        $sql = "INSERT INTO producten (product, type, fabriek) VALUES (?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        if ($stmt) {
                            $stmt->bind_param("sss", $product, $type, $fabriek);
                            if ($stmt->execute()) {
                                echo "<div class='alert alert-success'>Product added successfully.</div>";
                            } else {
                                echo "<div class='alert alert-danger'>Error adding product: " . $stmt->error . "</div>";
                            }
                            $stmt->close();
                        } else {
                            echo "<div class='alert alert-danger'>Error preparing statement: " . $conn->error . "</div>";
                        }
                    }
                    $check_stmt->close();
                } else {
                    echo "<div class='alert alert-warning'>Please fill in all fields.</div>";
                }
            }
        }

        // Fetch and display products
        $sql_select = "SELECT * FROM producten"; // Adjust this to your table name
        $result = $conn->query($sql_select);

        if ($result->num_rows > 0) {
            echo '<table class="table table-bordered">
                  <thead>
                      <tr>
                          <th>id</th>
                          <th>Product</th>
                          <th>Type</th>
                          <th>Fabriek</th>
                      </tr>
                  </thead>
                  <tbody>';
            while ($row = $result->fetch_assoc()) {
                echo '<tr>
                          <td>' . $row["id"] . '</td>
                          <td>' . $row["product"] . '</td>
                          <td>' . $row["type"] . '</td>
                          <td>' . $row["fabriek"] . '</td>
                      </tr>';
            }
            echo '</tbody></table>';
        } else {
            echo "<div class='alert alert-info'>No products found.</div>";
        }

        $conn->close(); // Close the database connection
        ?>
    </div>

    <!-- Bootstrap JS and other dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
