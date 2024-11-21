<?php
session_start();
include 'connectie.php'; // Ensure this file correctly connects to your database

// Add product to cart when the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['product_id'])) {
        $product_id = (int)$_POST['product_id'];
        
        // Fetch product details from database
        $stmt = $conn->prepare("SELECT id, product AS naam, prijs FROM producten WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();
            
            // Initialize cart session if not set
            if (!isset($_SESSION['winkelmand'])) {
                $_SESSION['winkelmand'] = [];
            }

            // Check if product is already in cart and update quantity, or add as new
            $product_found = false;
            foreach ($_SESSION['winkelmand'] as &$item) {
                if ($item['id'] == $product['id']) {
                    $item['aantal']++;
                    $product_found = true;
                    break;
                }
            }

            if (!$product_found) {
                $_SESSION['winkelmand'][] = [
                    'id' => $product['id'],
                    'naam' => $product['naam'],
                    'prijs' => $product['prijs'],
                    'aantal' => 1
                ];
            }

            echo json_encode(['success' => true, 'message' => 'Product toegevoegd aan winkelmandje']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Product niet gevonden']);
        }
        exit;
    }
}

// Update quantity in cart via AJAX
if (isset($_POST['update_aantal'], $_POST['product_id'], $_POST['nieuw_aantal'])) {
    $product_id = (int)$_POST['product_id'];
    $nieuw_aantal = max(1, (int)$_POST['nieuw_aantal']); // Set minimum quantity to 1

    foreach ($_SESSION['winkelmand'] as &$item) {
        if ($item['id'] == $product_id) {
            $item['aantal'] = $nieuw_aantal;
            echo json_encode(['success' => true, 'message' => 'Aantal bijgewerkt']);
            exit;
        }
    }
    echo json_encode(['success' => false, 'message' => 'Product niet in winkelmandje']);
    exit;
}

include 'navbar.php';
?>

<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Winkelmandje</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Winkelmandje</h2>

        <?php if (!empty($_SESSION['winkelmand'])): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Aantal</th>
                        <th>Prijs</th>
                        <th>Totaal</th>
                        <th>Actie</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totaal = 0;
                    foreach ($_SESSION['winkelmand'] as $item):
                        $product_totaal = $item['prijs'] * $item['aantal'];
                        $totaal += $product_totaal;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['naam']); ?></td>
                        <td>
                            <input type="number" class="form-control update-aantal" data-product-id="<?php echo $item['id']; ?>" value="<?php echo $item['aantal']; ?>" min="1">
                        </td>
                        <td>€<?php echo number_format($item['prijs'], 2); ?></td>
                        <td>€<?php echo number_format($product_totaal, 2); ?></td>
                        <td>
                            <button data-product-id="<?php echo $item['id']; ?>" class="btn btn-danger verwijder-knop">Verwijder</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <h3>Totaal: €<?php echo number_format($totaal, 2); ?></h3>
        <?php else: ?>
            <p>Je winkelmandje is leeg.</p>
        <?php endif; ?>

        <a href="bestellen.php" class="btn btn-primary">Verder winkelen</a>
        <a href="bestellingen.php" class="btn btn-success">Afrekenen</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.update-aantal').on('change', function() {
                var product_id = $(this).data('product-id');
                var nieuw_aantal = $(this).val();

                $.ajax({
                    url: 'winkelmand.php',
                    type: 'POST',
                    data: {
                        update_aantal: true,
                        product_id: product_id,
                        nieuw_aantal: nieuw_aantal
                    },
                    success: function(response) {
                        var result = JSON.parse(response);
                        if (result.success) {
                            alert(result.message);
                            location.reload();
                        } else {
                            alert('Er is een fout opgetreden: ' + result.message);
                        }
                    },
                    error: function() {
                        alert('Er is een fout opgetreden.');
                    }
                });
            });

            // Remove item
            $('.verwijder-knop').on('click', function() {
                var product_id = $(this).data('product-id');
                $.ajax({
                    url: 'winkelmand.php',
                    type: 'POST',
                    data: {
                        update_aantal: true,
                        product_id: product_id,
                        nieuw_aantal: 0 // Setting to 0 will remove the item
                    },
                    success: function(response) {
                        var result = JSON.parse(response);
                        if (result.success) {
                            alert(result.message);
                            location.reload();
                        } else {
                            alert('Er is een fout opgetreden: ' + result.message);
                        }
                    },
                    error: function() {
                        alert('Er is een fout opgetreden.');
                    }
                });
            });
        });
    </script>
</body>
</html>


