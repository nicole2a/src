<!DOCTYPE html>
<html>
<head>
	<title>LOGIN</title>
	<link rel="stylesheet" type="text/css" href="login.css">
</head>
<body>
     <form action="login2.php" method="post">
     	<h2>LOGIN</h2>
     	<?php if (isset($_GET['error'])) { ?>
     		<p class="error"><?php echo $_GET['error']; ?></p>
     	<?php } ?>
     	<label>E-mail</label>
     	<input type="text" name="uname" placeholder="gebruiker@gmail.com"><br>

     	<label>User Name</label>
     	<input type="password" name="password" placeholder="Voorbeeld123!"><br>

		<a href="logged_in.php">
     	<button>Login</button>
		</a>
		<a href="sign-up.php">Don't have an account? Click here to register.</a>
     </form>
</body>
</html>
