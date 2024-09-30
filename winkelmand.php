<?php
session_start();

// Voeg producten toe aan winkelmandje als het formulier wordt ingediend
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['product_id'])) {
        $product_id = $_POST['product_id'];
        $product_naam = $_POST['product_naam'];
        $product_prijs = $_POST['product_prijs'];

        // Controleer of het winkelmandje al bestaat, zo niet, maak het aan
        if (!isset($_SESSION['winkelmand'])) {
            $_SESSION['winkelmand'] = [];
        }

        // Controleer of het product al in de winkelmand zit
        $product_bestaat = false;
        foreach ($_SESSION['winkelmand'] as &$item) {
            if ($item['id'] == $product_id) {
                $item['aantal'] += 1;  // Verhoog aantal als het al bestaat
                $product_bestaat = true;
                break;
            }
        }

        // Als het product nog niet in het winkelmandje zit, voeg het toe
        if (!$product_bestaat) {
            $_SESSION['winkelmand'][] = [
                'id' => $product_id,
                'naam' => $product_naam,
                'prijs' => $product_prijs,
                'aantal' => 1
            ];
        }
    }
}

// Winkelmandje weergeven
include 'navbar.php';
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Winkelmandje</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome voor winkelmand icoon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Winkelmandje</h2>

        <?php if (isset($_SESSION['winkelmand']) && count($_SESSION['winkelmand']) > 0): ?>
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
                        <td><?php echo $item['naam']; ?></td>
                        <td><?php echo $item['aantal']; ?></td>
                        <td>€<?php echo number_format($item['prijs'], 2); ?></td>
                        <td>€<?php echo number_format($product_totaal, 2); ?></td>
                        <td>
                            <form action="verwijder.php" method="post">
                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" class="btn btn-danger">Verwijder</button>
                            </form>
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
</body>
</html>