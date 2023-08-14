<?php
  class Usuario {
    public $username = "";
    public $password = "";

    function __construct(){}

    public function login($pUsername, $pPassword){
      $this->username = $pUsername;
      $this->password = $pPassword;
    }
  }

  class Response{
    public $username = "";
    public $userType = "Vendedor";
    public $PK_Usuario = "";
    public $PK_Type = "";
  }

  if(isset($_POST)){
    include 'querys.php';

    $usuario = new Usuario();
    $usuario->login($_POST["username"], $_POST["password"]);

    $respuesta = new Response();
    $respuesta = Database::loginComprador($usuario, $respuesta);

    echo json_encode($respuesta);
  }
?>