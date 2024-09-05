
<?php

include "connectie.php";

if(isset($_POST['submit'])){
 $product = $_POST['product'];
 $type = $_POST['type'];
 $fabriek = $_POST['fabriek'];
 $voorraad_product = $_POST['voorraad_product'];
}

$sql = "INSERT INTO 'producten' ('product','type','fabriek','voorraad_product') VALUES ('$Accuboorhamer','$WX382','$Worx','$')"



if($result == TRUE) {
  echo "table created succesfully";
}
else{
  echo "ERROR" . $sql . "<br>". $conn->error;
}

$conn->close();
?>
