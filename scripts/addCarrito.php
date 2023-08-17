<?php
  class carrito{
    public $FK_Producto = "";
    public $FK_Usuario = "";

    public function insertCarrito($pFK_Producto, $pFK_Usuario){
      $this->FK_Producto = $pFK_Producto;
      $this->FK_Usuario = $pFK_Usuario;
    }
  }

  if(isset($_POST)){
    include 'querys.php';
    $_POST = json_decode(file_get_contents('php://input'),true);
    $FK_Producto = $_POST["FK_Producto"];
    $FK_Usuario = $_POST["FK_Usuario"];

    $carrito = new carrito();

    $carrito->insertCarrito($FK_Producto, $FK_Usuario);
    echo Database::insertCarrito($carrito);
  }
?>