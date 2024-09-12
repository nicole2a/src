<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();  // Ensure no further code is executed after the redirect
}
?>

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
    <!-- Your page content goes here -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container-fluid">
        <a class="navbar-brand" href="home.php">tools_for_ever</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link active" href="producten.php">bestellingen</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="vooraad.php">voorraad</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="bestellen.php">bestellen</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="logout.php">log uit</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
</body>
</html>
