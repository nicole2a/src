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

        <!-- Form for Adding New Product -->
        <div class="mb-4">
            <h3>Add New Product</h3>
            <form action="index-logged-in.php" method="post">
                <div class="mb-3">
                    <label for="product" class="form-label">Product Name</label>
                    <input type="text" class="form-control" id="product" name="product" required>
                </div>
                <div class="mb-3">
                    <label for="type" class="form-label">Product Type</label>
                    <input type="text" class="form-control" id="type" name="type" required>
                </div>
                <div class="mb-3">
                    <label for="fabriek" class="form-label">Fabriek</label>
                    <input type="text" class="form-control" id="fabriek" name="fabriek" required>
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

        // SQL query to get product details, stock, and locations
        $sql = "
        SELECT
            p.product AS product_name, 
            p.type AS product_type,
            v.voorraad AS voorraad,
            l.locatie AS locatie,
            pl.aantal AS aantal_in_locatie
        FROM producten p
        JOIN voorraad_has_producten vhp ON p.id = vhp.producten_id
        JOIN voorraad v ON vhp.voorraad_id = v.id
        JOIN producten_has_locaties pl ON p.id = pl.producten_id
        JOIN locaties l ON pl.locaties_id = l.id
        ";

        // Execute the query
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table class='table table-striped'>";
            echo "<thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Product Type</th>
                        <th>Voorraad</th>
                        <th>Locatie</th>
                        <th>Aantal in Locatie</th>
                    </tr>
                  </thead>";
            echo "<tbody>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['product_type']) . "</td>";
                echo "<td>" . htmlspecialchars($row['voorraad']) . "</td>";
                echo "<td>" . htmlspecialchars($row['locatie']) . "</td>";
                echo "<td>" . htmlspecialchars($row['aantal_in_locatie']) . "</td>";
                echo "</tr>";
            }

            echo "</tbody>";
            echo "</table>";
        } else {
            echo "<div class='alert alert-warning'>No products found.</div>";
        }

        $conn->close(); // Close the database connection
        ?>
    </div>

    <!-- Bootstrap JS and other dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
