<?php
 if(isset($_POST)){
    include 'querys.php';
    $_POST = json_decode(file_get_contents('php://input'),true);
    $FK_Producto = $_POST["FK_Producto"];

    echo json_encode(Database::getProduct($FK_Producto));
  }
?>