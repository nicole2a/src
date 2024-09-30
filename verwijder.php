<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    // Zoek het product en verwijder het uit de sessie
    foreach ($_SESSION['winkelmand'] as $key => $item) {
        if ($item['id'] == $product_id) {
            unset($_SESSION['winkelmand'][$key]);
            break;
        }
    }

    // Verwijs terug naar het winkelmandje
    header('Location: winkelmand.php');
    exit;
}
