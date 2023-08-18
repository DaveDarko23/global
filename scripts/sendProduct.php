<?php
  if(isset($_POST)){
    include 'querys.php';
    $_POST = json_decode(file_get_contents('php://input'),true);
    $PK_Carrito = $_POST["PK_Carrito"];

    echo Database::sendBuy($PK_Carrito);
  }
?>