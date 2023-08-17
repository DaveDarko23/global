<?php
  include 'querys.php';
  if(isset($_POST)){
    echo json_encode(Database::getProductList(" AND stock > 0"));
  }
?>