<?php
  include 'querys.php';
  if(isset($_POST)){
    $_POST = json_decode(file_get_contents('php://input'),true);
    $PK_Vendedor = $_POST["PK_Vendedor"];
    if(isset($_POST["busqueda"])){
      echo json_encode(Database::getProductList(" AND PK_Administrador = ".$PK_Vendedor." AND producto.Nombre LIKE '%".$_POST["busqueda"]."%';"));
    }else{
      echo json_encode(Database::getProductList(" AND PK_Administrador = ".$PK_Vendedor));
    }
  }
?>