<?php

include "connectie.php";

if(isset($_GET['id'])) {
    $product_id = $_GET['id'];

    $result = conn->query($sql);

    if ($result == TRUE){
     echo "data deleted succesfully"
    }else{
        echo "error" . $sql . "<br>" . $conn->error;
    }
}

?>