<?php
    if(isset($_POST)){
      include 'querys.php';
      $_POST = json_decode(file_get_contents('php://input'),true);
      $FK_Usuario = $_POST["FK_Usuario"];
      $userType = $_POST["field"];

      if(strcmp($userType,"Vendedor")===0){
        echo json_encode(Database::getVentas($FK_Usuario));
      }else{
        echo json_encode(Database::getCarritoList($FK_Usuario, ">"));
      }

    }
?>