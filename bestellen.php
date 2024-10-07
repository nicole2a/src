<?php
include 'navbar.php'; // Ensure session is started in navbar.php
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
        /* Extra styles to enhance the product page */
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
        .cart-message {
            display: none;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <!-- Container for products -->
    <div class="container mt-4">  
        <h2>Onze Producten</h2>

        <!-- Location selection -->
        <form action="bestellen.php" method="get">
            <div class="mb-4">
                <label for="locatie" class="form-label">Selecteer locatie:</label>
                <select name="locatie_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Alle locaties</option>
                    <?php
                    // Database connection and query for locations
                    include 'connectie.php'; // Ensure this file contains your database credentials
                    $sql_locaties = "SELECT * FROM locaties";
                    $result_locaties = $conn->query($sql_locaties);
                    
                    // Check if there are locations and display them in the dropdown
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

        <!-- Product display as cards -->
        <div class="row">
            <?php
            // Fetch products based on selected location
            $locatie_id = isset($_GET['locatie_id']) ? (int)$_GET['locatie_id'] : 0;
            $sql_select = "SELECT p.*, l.locatie 
                           FROM producten p 
                           LEFT JOIN locaties l ON p.locatie_id = l.id" 
                           . ($locatie_id > 0 ? " WHERE p.locatie_id = $locatie_id" : "");
            $result = $conn->query($sql_select);

            // Check if there are products and display them as cards
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Check if there is an image, otherwise use a placeholder
                    $product_image = !empty($row['image_url']) ? $row['image_url'] : 'https://via.placeholder.com/200x200.png?text=No+Image';

                    echo '
                    <div class="col-md-4">
                        <div class="card product-card">
                            <img src="' . htmlspecialchars($product_image) . '" class="card-img-top" alt="' . htmlspecialchars($row["product"]) . '">
                            <div class="card-body">
                                <h5 class="card-title">' . htmlspecialchars($row["product"]) . '</h5>
                                <p class="card-text">Type: ' . htmlspecialchars($row["type"]) . '</p>
                                <p class="card-text">Fabriek: ' . htmlspecialchars($row["fabriek"]) . '</p>
                                <p class="card-text">Locatie: ' . htmlspecialchars($row["locatie"]) . '</p>
                                <p class="price">€' . number_format($row["prijs"], 2) . '</p>
                                <button type="button" class="btn btn-success add-to-cart" data-product-id="' . $row["id"] . '" data-product-naam="' . htmlspecialchars($row["product"]) . '" data-product-prijs="' . $row["prijs"] . '">Toevoegen aan winkelmandje</button>
                                <div class="cart-message alert alert-success" role="alert">Product toegevoegd aan winkelmandje!</div>
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

    <!-- jQuery for AJAX request -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            // Add to cart button click handler
            $('.add-to-cart').click(function() {
                var product_id = $(this).data('product-id');
                var product_naam = $(this).data('product-naam');
                var product_prijs = $(this).data('product-prijs');
                var button = $(this); // The button clicked

                $.ajax({
                    url: 'winkelmand.php',
                    type: 'POST',
                    data: {
                        product_id: product_id,
                        product_naam: product_naam,
                        product_prijs: product_prijs
                    },
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            button.siblings('.cart-message').show().delay(2000).fadeOut();
                            
                            // Optionally update cart count or cart UI here if you want
                            // You can increment the cart count or show a success message globally
                            // $('#cart-count').text(response.cartCount);
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
