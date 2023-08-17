<?php
  if(isset($_POST)){
    include("querys.php");
    include("PruebaPDF.php");
    include("prueba.php");
    $_POST = json_decode(file_get_contents('php://input'),true);
    $PK_Usuario = $_POST["PK_Usuario"];
    $precioTotal = $_POST["precioTotal"];
    $array =  $_POST["array"]; 
    $array2 = $array["array"];

    $random = rand(0, 99999999999);
    $url = "http://192.168.100.6/global/pdf/mail-".$random.".pdf";

    foreach ($array2 as $user){
      list($id, $name, $Cantidad, $precio) = $user;
      if(Database::updateStock($id, $Cantidad) === 200){
        if(Database::insertPDF($PK_Usuario, $url) === 200){
          Database::cleanCarrito($PK_Usuario);
          echo 200;
        }
      }
    }  

    $estado = Database::getState($PK_Usuario);
    $domicilio = Database::getDomicilio($PK_Usuario);

    generatePdf($random);
    generateContentPdf($array2,$precioTotal, $estado, $domicilio, $random);

    $email = Database::getEmail($PK_Usuario);

    sendMail($random,$email);
  }
    
  //{"array":[["1"],["5"]]}
  //{"nombre":"usuario","array":{"array":[["1"],["5"]]}}
  //{"array":[["1"],["5"]]}
?>

