<?php
    class Usuario {
      public $email = "";
      public $username = "";
      public $password = "";
      public $name = "";
      public $lastName = "";
      public $userType = "";
  
      function __construct(){}
  
      public function login($pEmail, $pUsername, $pPassword, $pName, $pLastName, $pUserType){
        $this->email = $pEmail;
        $this->username = $pUsername;
        $this->password = $pPassword;
        $this->name = $pName;
        $this->lastName = $pLastName;
        $this->userType = $pUserType;
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
      $usuario->login(
        $_POST["email"],
        $_POST["username"], 
        $_POST["password"],
        $_POST["name"],
        $_POST["last-name"],
        $_POST["type"]);
  
      $response = new Response();

      $respuesta = Database::register($usuario, $response);
  
      echo json_encode($respuesta);
    }
?>