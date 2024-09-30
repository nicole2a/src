<!DOCTYPE html>
<html>
<head>
    <title>LOGIN</title>
    <link rel="stylesheet" type="text/css" href="login.css">
</head>
<body>
    <form action="admin2.php" method="post">
        <h2>LOGIN</h2>
        <?php if (isset($_GET['error'])) { ?>
            <p class="error"><?php echo $_GET['error']; ?></p>
        <?php } ?>
        <label>E-mail</label>
        <input type="text" name="email" placeholder="gebruiker@gmail.com" required><br>

        <label>Password</label>
        <input type="password" name="password" placeholder="Voorbeeld123!" required><br>

        <button type="submit">Login</button>
    </form>
    <a href="sign-up.php">Don't have an account? Click here to register.</a>
</body>
</html>

