<?php
  include 'querys.php';
  
  if(isset($_POST)){
    $_POST = json_decode(file_get_contents('php://input'),true);
    if(isset($_POST["busqueda"])){
      echo json_encode(Database::getProductList(" AND stock > 0 AND producto.nombre LIKE '%".$_POST["busqueda"]."%';"));
    }else{
      echo json_encode(Database::getProductList(" AND stock > 0"));
    }
  }
?>