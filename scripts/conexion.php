<?php
  $hostname = 'localhost';
  $database = 'comercioglobal';
  $username = 'root';
  $password = '';

  $conexion = new mysqli($hostname, $username, $password, $database);

  if($conexion->connect_errno)
    echo "El sitio web no está funcionando";
?>