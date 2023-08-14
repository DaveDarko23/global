<?php
  class Database {
    function __construct(){}

    public static function loginComprador($usuario, $response){
      try{
          include 'conexion.php';
          $sentencia = $conexion->prepare("SELECT PK_Usuario FROM usuario WHERE Username='$usuario->username' AND password='$usuario->password'");
          $sentencia->execute();

          $resultado = $sentencia->get_result();

          if($fila=$resultado->fetch_assoc()){
            $PK = $fila["PK_Usuario"];

            $PK_Type = Database::getUserType($PK, "Vendedor", "PK_Administrador");
            if($PK_Type < 0){
              $PK_Type = Database::getUserType($PK, "Comprador", "PK_Comprador");
              $response->userType = "Comprador";
            } 
            
          }

          $response->username = $usuario->username;
          $response->PK_Usuario = $PK;
          $response->PK_Type = $PK_Type;

          $sentencia->close();
          $conexion->close();

          return $response;
      }catch(mysqli_sql_exception $e){
          $conexion->close();
          $response->PK_Usuario = -1;
          return $response;
      }
    }

    public static function getUserType($PK, $table, $field){
      try{
          include 'conexion.php';
          $sentencia = $conexion->prepare("SELECT $field FROM $table INNER JOIN usuario WHERE FK_Usuario = $PK");
          $sentencia->execute();

          $resultado = $sentencia->get_result();

          if($fila=$resultado->fetch_assoc()){
            return $fila[$field];
          }

          return -1;
      }catch(mysqli_sql_exception $e){
          $conexion->close();
          return -1;
      }
    }

    public static function getPK($usuario){
      try{
          include 'conexion.php';
          $sentencia = $conexion->prepare("SELECT PK_Usuario FROM usuario WHERE Username='$usuario->username' and password='$usuario->password'");
          $sentencia->execute();

          $resultado = $sentencia->get_result();

          if($fila=$resultado->fetch_assoc()){
            return $fila['PK_Usuario'];
          }
      }catch(mysqli_sql_exception $e){
          $conexion->close();
          return -1;
      }
    }

    private static function insertUsuario($PK, $userType){
      try{
        include 'conexion.php';
        
        $retorno = 0;

        $query = "INSERT INTO $userType (
          FK_Usuario) 
        VALUES 
            (?)";
    
        $sentencia = $conexion->prepare($query);
        $sentencia->bind_param('i', $PK);
    
        if($sentencia->execute()){
          $retorno = 200;
        }
        
        return $retorno;
      }catch(mysqli_sql_exception $e){
        $conexion->close();
        return -1;
      }
    }

    public static function register($usuario, $response){
      try{
        include 'conexion.php';

        $query = "INSERT INTO usuario (
          correo, Username, password, nombre, apellido) 
      VALUES 
          (?, ?, ?, ?, ?)";

      $sentencia = $conexion->prepare($query);
      $sentencia->bind_param('sssss', $usuario->email, $usuario->username,$usuario->password, $usuario->name, $usuario->lastName);

      if($sentencia->execute()){
        $PK = Database::getPK($usuario);

        Database::insertUsuario($PK, $usuario->userType);
      }

      $field = "PK_Comprador";
      if (strcmp($usuario->userType, "Vendedor") === 0) {
        $field = "PK_Administrador";
      }

      $PK_Type = Database::getUserType($PK, $usuario->userType, $field);
      
      $response->username = $usuario->username;
      $response->PK_Usuario = $PK;
      $response->PK_Type = $PK_Type;
      $response->userType = $usuario->userType;

      $sentencia->close();
      $conexion->close();

      return $response;
      }catch(mysqli_sql_exception $e){
        $conexion->close();
        return -1;
      }
    }

    public static function getProductList(){
      include 'conexion.php';

      $json = array();
    
      $consulta = "SELECT producto.*, username FROM producto INNER JOIN vendedor INNER JOIN usuario WHERE FK_Vendedor = PK_Administrador AND PK_Usuario = FK_Usuario;";
      $resultado = mysqli_query($conexion, $consulta);
    
      while($registro = mysqli_fetch_array($resultado)){
        $result["PK_Producto"] = $registro['PK_Producto'];
        $result["imagen"] = $registro['Imagen'];
        $result["nombre"] = $registro['Nombre'];
        $result["descripcion"] = $registro['Descripcion'];
        $result["precio"] = $registro['precio'];
        $result["stock"] = $registro['stock'];
        $result["categoria"] = $registro['categoria'];
        $result["username"] = $registro['username'];
        $result["FK_Vendedor"] = $registro['FK_Vendedor'];
        $json['producto'][] = $result;
      }
    
      mysqli_close($conexion);
      return $json;
    }

    public static function insertProduct($producto){
      try{
        include 'conexion.php';

        $query = "INSERT INTO producto (
          Imagen, Nombre, Descripcion, precio, stock, categoria, FK_Vendedor) 
        VALUES 
            (?,?,?,?,?,?,?)";
    
        $sentencia = $conexion->prepare($query);
        $sentencia->bind_param('sssdisi', $producto->imagen,$producto->name,$producto->descripcion,$producto->precio,$producto->stock,$producto->categoria,$producto->FK_Vendedor);
    
        if($sentencia->execute()){
          $retorno = 200;
        }
        
        return $retorno;
      }catch(mysqli_sql_exception $e){
        $conexion->close();
        return -1;
      }
    }

    public static function insertDeseo($deseo){
      try{
        include 'conexion.php';

        $query = "INSERT INTO deseos (FK_Usuario, FK_Producto) 
        VALUES 
            (?,?)";
    
        $sentencia = $conexion->prepare($query);
        $sentencia->bind_param('ii',  $deseo->FK_Usuario ,$deseo->FK_Producto);
    
        if($sentencia->execute()){
          $retorno = 200;
        }
        
        return $retorno;
      }catch(mysqli_sql_exception $e){
        $conexion->close();
        return ;
      }
    }

    public static function getWishesList($FK_Usuario){
      include 'conexion.php';

      $json = array();
    
      $consulta = "SELECT producto.*, username FROM deseos 
      INNER JOIN producto 
      INNER JOIN vendedor 
      INNER JOIN usuario 
      WHERE deseos.FK_Producto = producto.PK_Producto 
      AND vendedor.PK_Administrador = producto.FK_Vendedor 
      AND usuario.PK_Usuario = vendedor.FK_Usuario 
      AND deseos.FK_Usuario = ".$FK_Usuario;
      
      $resultado = mysqli_query($conexion, $consulta);
    
      while($registro = mysqli_fetch_array($resultado)){
        $result["PK_Producto"] = $registro['PK_Producto'];
        $result["imagen"] = $registro['Imagen'];
        $result["nombre"] = $registro['Nombre'];
        $result["descripcion"] = $registro['Descripcion'];
        $result["precio"] = $registro['precio'];
        $result["stock"] = $registro['stock'];
        $result["categoria"] = $registro['categoria'];
        $result["username"] = $registro['username'];
        $result["FK_Vendedor"] = $registro['FK_Vendedor'];
        $json['producto'][] = $result;
      }
    
      mysqli_close($conexion);
      return $json;
    }
  }
?>