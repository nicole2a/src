<?php 
session_start(); 
include "connectie.php";

if (isset($_POST['uname']) && isset($_POST['password_hash'])) {

	function validate($data){
       $data = trim($data);
	   $data = stripslashes($data);
	   $data = htmlspecialchars($data);
	   return $data;
	}

	$uname = validate($_POST['uname']);
	$pass = validate($_POST['password_hash']);

	if (empty($uname)) {
		header("Location: login.php?error=E-mail is required");
	    exit();
	}else if(empty($pass)){
        header("Location: login.php?error=Password is required");
	    exit();
	}else{
		$sql = "SELECT * FROM MyGuests WHERE password='$password_hash'";

		$result = $conn->query($sql);
    if(!$result){
      die ("invalid query!");
    }

		if (mysqli_num_rows($result) === 1) {
			$row = mysqli_fetch_assoc($result);
            if ($row['email'] === $uname && $row['password_hash'] === $password_hash) {
            	$_SESSION['email'] = $row['email'];
            	$_SESSION['name'] = $row['name'];
            	$_SESSION['id'] = $row['id'];
            	header("Location: login2.php");
		        exit();
            }else{
				header("Location: login.php?error=Incorect E-mail or password");
		        exit();
			}
		}else{
			header("Location: login.php?error=Incorect E-mail or password");
	        exit();
		}
	}
	
}else{
	header("Location: login.php");
	exit();
}