<?php

include "connectie.php"

if(isset($_POST['update'])){
  $product = $_POST['product'];
  $type = $_POST['type'];
  $fabriek = $_POST['fabriek'];
  $voorraad_product = $_POST['voorraad_product'];
}

$sql = "UPDATE 'producten' SET 'product' = '$product', 'type' = '$type', 'fabriek'= '$fabriek' , 'voorraad_product'= '$voorraad_product'"

$result = $conn->query($sql)
