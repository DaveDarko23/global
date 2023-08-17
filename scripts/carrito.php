<?php
  if(isset($_POST)){
    include 'querys.php';
    $_POST = json_decode(file_get_contents('php://input'),true);
    $FK_Usuario = $_POST["FK_Usuario"];
    $FK_Producto = $_POST["FK_Producto"];
    $action = $_POST["action"];

    if($action === "0") echo json_encode(Database::getCarritoList($FK_Usuario, "="));
    if($action === "1") echo json_encode(Database::updatePlusCarrito($FK_Usuario,$FK_Producto));
    if($action === "2") echo json_encode(Database::updateLessCarrito($FK_Usuario,$FK_Producto));
    if($action === "3") echo json_encode(Database::deleteCarrito($FK_Usuario,$FK_Producto));
  }
?>