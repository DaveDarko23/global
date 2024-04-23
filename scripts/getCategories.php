<?php
  include ('querys.php');
  echo json_encode(Database::getCategories());
?>