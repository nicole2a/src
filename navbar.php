<?php

// Functie om het aantal producten in het winkelmandje te tellen
function aantalProductenInWinkelmand() {
    if (isset($_SESSION['winkelmand'])) {
        $aantal = 0;
        foreach ($_SESSION['winkelmand'] as $item) {
            $aantal += $item['aantal'];
        }
        return $aantal;
    }
    return 0;
}

// Verkrijg het aantal producten in het winkelmandje
$aantal_producten = aantalProductenInWinkelmand();
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="bestellen.php">Tools_for_ever</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="bestellen.php">Winkel</a>
                </li>
</ul>
<ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="winkelmand.php">Winkelmandje <?php if ($aantal_producten > 0) { echo "($aantal_producten)"; } ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="loguit.php">Log uit</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
