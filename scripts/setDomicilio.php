<?php
  if(isset($_POST)){
    include 'querys.php';
    $domicilio = $_POST["domicilio"];
    $estado = $_POST["estado"];
    $id = $_POST["id"];

    echo Database::insertDomicilio($id, $domicilio, $estado);
  }
?>