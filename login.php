<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="login.css">
    <style>
        .error { color: red; }
        /* Add additional styling as needed */
    </style>
</head>
<body>
    <form action="login2.php" method="post">
        <h2>Login</h2>
        <?php if (isset($_GET['error'])) { ?>
            <p class="error"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php } ?>
        <div>
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" placeholder="gebruiker@gmail.com" required aria-required="true">
        </div>

        <div>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Voorbeeld123!" required aria-required="true">
        </div>

        <button type="submit">Login</button>
    </form>
    <a href="sign-up.php">Don't have an account? Click here to register.</a>
</body>
</html>
