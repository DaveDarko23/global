<?php
  $hostname = '10.0.0.5';
  $database = 'comercioglobal';
  $username = 'dave';
  $password = '1234';

  $conexion = new mysqli($hostname, $username, $password, $database);

  if($conexion->connect_errno)
    echo "El sitio web no está funcionando";
?>