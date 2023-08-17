<?php
 if(isset($_POST)){
    include 'querys.php';

    echo json_encode(Database::getStates());
  }
?>