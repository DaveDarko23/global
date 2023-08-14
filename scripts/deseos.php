<?php
  if(isset($_POST)){
    include 'querys.php';
    $_POST = json_decode(file_get_contents('php://input'),true);
    $FK_Usuario = $_POST["FK_Usuario"];

    echo json_encode(Database::getWishesList($FK_Usuario));
  }
?>