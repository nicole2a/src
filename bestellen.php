<?php
include 'navbar.php';  // Zorg ervoor dat de sessie hier is gestart in navbar.php
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tools For Ever - Winkel</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Extra stijlen om de productpagina mooier te maken */
        .product-card {
            margin-bottom: 20px;
        }
        .product-card img {
            max-height: 200px;
            object-fit: cover;
        }
        .card-body h5 {
            font-size: 1.25rem;
            font-weight: bold;
        }
        .price {
            font-size: 1.5rem;
            color: #28a745;
        }
    </style>
</head>
<body>

    <!-- Container voor de producten -->
    <div class="container mt-4">  
        <h2>Onze Producten</h2>

        <!-- Locatie selectie -->
        <form action="bestellen.php" method="get">
            <div class="mb-4">
                <label for="locatie" class="form-label">Selecteer locatie:</label>
                <select name="locatie_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Alle locaties</option>
                    <?php
                    // Databaseverbinding en query voor locaties
                    include 'connectie.php';  // Zorg ervoor dat dit bestand je databasegegevens bevat
                    $sql_locaties = "SELECT * FROM locaties";
                    $result_locaties = $conn->query($sql_locaties);
                    
                    // Check of er locaties zijn en toon ze in de dropdown
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

        <!-- Producten weergave als kaarten -->
        <div class="row">
            <?php
            // Haal producten op op basis van de geselecteerde locatie
            $locatie_id = isset($_GET['locatie_id']) ? (int)$_GET['locatie_id'] : 0;
            $sql_select = "SELECT p.*, l.locatie 
                           FROM producten p 
                           LEFT JOIN locaties l ON p.locatie_id = l.id" 
                           . ($locatie_id > 0 ? " WHERE p.locatie_id = $locatie_id" : "");
            $result = $conn->query($sql_select);

            // Check of er producten zijn en toon ze in de kaarten
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Dummy-afbeelding of een dynamisch toegevoegde afbeelding
                    $product_image = 'https://via.placeholder.com/200x200.png?text=' . urlencode($row["product"]);

                    echo '
                    <div class="col-md-4">
                        <div class="card product-card">
                            <img src="' . $product_image . '" class="card-img-top" alt="' . $row["product"] . '">
                            <div class="card-body">
                                <h5 class="card-title">' . $row["product"] . '</h5>
                                <p class="card-text">Type: ' . $row["type"] . '</p>
                                <p class="card-text">Fabriek: ' . $row["fabriek"] . '</p>
                                <p class="card-text">Locatie: ' . $row["locatie"] . '</p>
                                <p class="price">€' . number_format($row["prijs"], 2) . '</p> <!-- Prijs vanuit database -->
                                <form action="winkelmand.php" method="post">
                                    <input type="hidden" name="product_id" value="' . $row["id"] . '">
                                    <input type="hidden" name="product_naam" value="' . $row["product"] . '">
                                    <input type="hidden" name="product_prijs" value="' . $row["prijs"] . '">
                                    <button type="submit" class="btn btn-success">Toevoegen aan winkelmandje</button>
                                </form>
                            </div>
                        </div>
                    </div>';
                }
            } else {
                echo "<p class='text-center'>Geen producten gevonden.</p>";
            }

            $conn->close();
            ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
