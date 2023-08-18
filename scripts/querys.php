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
            $conexion->close();
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
            $sentencia->close();
          $conexion->close();
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
        $sentencia->close();
          $conexion->close();
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

    public static function getProductList($condition){
      include 'conexion.php';

      $json = array();
    
      $consulta = "SELECT producto.*, username FROM producto INNER JOIN vendedor INNER JOIN usuario WHERE FK_Vendedor = PK_Administrador AND PK_Usuario = FK_Usuario".$condition;
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

    public static function getProduct($PK){
      try{
        include 'conexion.php';
        $sentencia = $conexion->prepare("SELECT producto.*, username FROM producto INNER JOIN vendedor INNER JOIN usuario WHERE FK_Vendedor = PK_Administrador AND PK_Usuario = FK_Usuario AND PK_Producto = ". $PK);
        $sentencia->execute();

        $resultado = $sentencia->get_result();

        if($fila=$resultado->fetch_assoc()){
          $result["PK_Producto"] = $fila['PK_Producto'];
          $result["imagen"] = $fila['Imagen'];
          $result["nombre"] = $fila['Nombre'];
          $result["descripcion"] = $fila['Descripcion'];
          $result["precio"] = $fila['precio'];
          $result["stock"] = $fila['stock'];
          $result["categoria"] = $fila['categoria'];
          $result["username"] = $fila['username'];
          $result["FK_Vendedor"] = $fila['FK_Vendedor'];
          $sentencia->close();
          $conexion->close();
          return $result;
        }
      }catch(mysqli_sql_exception $e){
          $conexion->close();
          return -1;
      }
    }

    public static function getStates(){
      try{
        include 'conexion.php';
        $consulta = "SELECT * FROM entidad";
      
        $resultado = mysqli_query($conexion, $consulta);

        while($fila= mysqli_fetch_array($resultado)){
          $result["PK_Estado"] = $fila['PK_Estado'];
          $result["Nombre"] = $fila['Nombre'];
          $json['Estado'][] = $result;

        }
        $conexion->close();
        return $json;
      }catch(mysqli_sql_exception $e){
          $conexion->close();
          return -1;
      }
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
        $sentencia->close();
          $conexion->close();
        return $retorno;
      }catch(mysqli_sql_exception $e){
        $conexion->close();
        return -1;
      }
    }

    public static function editProduct($producto){
      try{
        include 'conexion.php';

        $query = "UPDATE producto SET
                Imagen='$producto->imagen',
                Nombre = '$producto->name',
                Descripcion = '$producto->descripcion',
                precio = '$producto->precio',
                stock = '$producto->stock',
                categoria = '$producto->categoria'
                WHERE PK_Producto = '$producto->PK_Producto'";

        $sentencia = $conexion->prepare($query);
    
        if($sentencia->execute()){
          $retorno = 200;
        }
        $sentencia->close();
          $conexion->close();
        return $retorno;
      }catch(mysqli_sql_exception $e){
        $conexion->close();
        return -1;
      }
    }

    public static function deleteProduct($FK_Producto){
      try{
        include 'conexion.php';

        $retorno = 0;

        $query = "UPDATE producto SET stock = 0 WHERE PK_Producto = ?";
    
        $sentencia = $conexion->prepare($query);
        $sentencia->bind_param('i', $FK_Producto);
    
        if($sentencia->execute()){
          $retorno = 200;
        }
        $sentencia->close();
          $conexion->close();
        return $retorno;
      }catch(mysqli_sql_exception $e){
        $conexion->close();
        return -1;
      }
    }

    public static function insertDomicilio($id, $domicilio, $estado){
      try{
        include 'conexion.php';

        $retorno = 0;

        $query = "UPDATE comprador SET domicilio = ?, FK_Estado = ? WHERE PK_Comprador = ?";
    
        $sentencia = $conexion->prepare($query);
        $sentencia->bind_param('sii', $domicilio, $estado, $id);
    
        if($sentencia->execute()){
          $retorno = 200;
        }
        $sentencia->close();
          $conexion->close();
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

    public static function insertCarrito($carrito){
      try{
        include 'conexion.php';

        $query = "INSERT INTO carrito (FK_Usuario, FK_Producto) 
        VALUES 
            (?,?)";
    
        $sentencia = $conexion->prepare($query);
        $sentencia->bind_param('ii',  $carrito->FK_Usuario ,$carrito->FK_Producto);
    
        if($sentencia->execute()){
          $retorno = 200;
        }
        $sentencia->close();
          $conexion->close();
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

    public static function getCarritoList($FK_Usuario, $type){
      include 'conexion.php';

      $json = array();
    
      $consulta = "SELECT producto.*, username, cantidad, status, pdf FROM carrito 
      INNER JOIN producto 
      INNER JOIN vendedor 
      INNER JOIN usuario 
      WHERE carrito.FK_Producto = producto.PK_Producto 
      AND vendedor.PK_Administrador = producto.FK_Vendedor 
      AND usuario.PK_Usuario = vendedor.FK_Usuario 
      AND carrito.status ".$type." 0 
      AND carrito.FK_Usuario = ".$FK_Usuario;
      
      $resultado = mysqli_query($conexion, $consulta);
    
      while($registro = mysqli_fetch_array($resultado)){
        $result["PK_Producto"] = $registro['PK_Producto'];
        $result["imagen"] = $registro['Imagen'];
        $result["nombre"] = $registro['Nombre'];
        $result["descripcion"] = $registro['Descripcion'];
        $result["precio"] = $registro['precio'];
        $result["stock"] = $registro['stock'];
        $result["status"] = $registro['status'];
        $result["categoria"] = $registro['categoria'];
        $result["username"] = $registro['username'];
        $result["cantidad"] = $registro['cantidad'];
        $result["pdf"] = $registro['pdf'];
        $result["FK_Vendedor"] = $registro['FK_Vendedor'];
        $json['producto'][] = $result;
      }
    
      mysqli_close($conexion);
      return $json;
    }

    public static function getVentas($FK_Usuario){
      include 'conexion.php';

      $json = array();
    
      $consulta = "SELECT producto.*, username, pdf, status,PK_Carrito FROM carrito 
      INNER JOIN producto 
      INNER JOIN vendedor 
      INNER JOIN usuario 
      WHERE carrito.FK_Producto = producto.PK_Producto 
      AND vendedor.PK_Administrador = producto.FK_Vendedor 
      AND usuario.PK_Usuario = vendedor.FK_Usuario 
      AND carrito.status > 0 
      AND PK_Administrador = ".$FK_Usuario;
      
      $resultado = mysqli_query($conexion, $consulta);
    
      while($registro = mysqli_fetch_array($resultado)){
        $result["PK_Producto"] = $registro['PK_Producto'];
        $result["PK_Carrito"] = $registro['PK_Carrito'];
        $result["imagen"] = $registro['Imagen'];
        $result["nombre"] = $registro['Nombre'];
        $result["descripcion"] = $registro['Descripcion'];
        $result["precio"] = $registro['precio'];
        $result["stock"] = $registro['stock'];
        $result["status"] = $registro['status'];
        $result["categoria"] = $registro['categoria'];
        $result["username"] = $registro['username'];
        $result["pdf"] = $registro['pdf'];
        $result["FK_Vendedor"] = $registro['FK_Vendedor'];
        $json['producto'][] = $result;
      }
    
      mysqli_close($conexion);
      return $json;
    }

    public static function updatePlusCarrito($FK_Usuario,$FK_Producto){
      try{
        include 'conexion.php';

        $retorno = 0;

        $query = "UPDATE carrito SET cantidad = cantidad + 1 WHERE FK_Producto = ? AND FK_Usuario=? AND Status=0";
    
        $sentencia = $conexion->prepare($query);
        $sentencia->bind_param('ii', $FK_Producto, $FK_Usuario);
    
        if($sentencia->execute()){
          $retorno = 200;
        }
        $sentencia->close();
          $conexion->close();
        return $retorno;
      }catch(mysqli_sql_exception $e){
        $conexion->close();
        return -1;
      }
    }

    public static function updateLessCarrito($FK_Usuario,$FK_Producto){
      try{
        include 'conexion.php';

        $retorno = 0;

        $query = "UPDATE carrito SET cantidad = cantidad - 1 WHERE FK_Producto = ? AND FK_Usuario=? AND Status=0";
    
        $sentencia = $conexion->prepare($query);
        $sentencia->bind_param('ii', $FK_Producto, $FK_Usuario);
    
        if($sentencia->execute()){
          $retorno = 200;
        }
        $sentencia->close();
          $conexion->close();
        return $retorno;
      }catch(mysqli_sql_exception $e){
        $conexion->close();
        return -1;
      }
    }

    public static function deleteCarrito($FK_Usuario,$FK_Producto){
      try{
        include 'conexion.php';

        $retorno = 0;

        $query = "DELETE FROM carrito WHERE FK_Producto = ? AND FK_Usuario=? AND Status=0";
    
        $sentencia = $conexion->prepare($query);
        $sentencia->bind_param('ii', $FK_Producto, $FK_Usuario);
    
        if($sentencia->execute()){
          $retorno = 200;
        }
        $sentencia->close();
          $conexion->close();
        return $retorno;
      }catch(mysqli_sql_exception $e){
        $conexion->close();
        return -1;
      }
    }

    public static function insertPDF($FK_Usuario,$url){
      try{
        include 'conexion.php';

        $retorno = 0;

        $query = "UPDATE carrito SET pdf = ? WHERE FK_Usuario=? AND Status=0";
    
        $sentencia = $conexion->prepare($query);
        $sentencia->bind_param('si', $url, $FK_Usuario);
    
        if($sentencia->execute()){
          $retorno = 200;
        }
        $sentencia->close();
          $conexion->close();
        return $retorno;
      }catch(mysqli_sql_exception $e){
        $conexion->close();
        return -1;
      }
    }

    public static function deleteDeseos($FK_Usuario,$FK_Producto){
      try{
        include 'conexion.php';

        $retorno = 0;

        $query = "DELETE FROM deseos WHERE FK_Producto = ? AND FK_Usuario=?";
    
        $sentencia = $conexion->prepare($query);
        $sentencia->bind_param('ii', $FK_Producto, $FK_Usuario);
    
        if($sentencia->execute()){
          $retorno = 200;
        }
        $sentencia->close();
          $conexion->close();
        
        return $retorno;
      }catch(mysqli_sql_exception $e){
        $conexion->close();
        return -1;
      }
    }

    public static function cleanCarrito($PK_Usuario){
      try{
        include 'conexion.php';

        $retorno = 0;

        $query = "UPDATE carrito SET status = 1 where FK_Usuario = ? AND status = 0;";
    
        $sentencia = $conexion->prepare($query);
        $sentencia->bind_param('i', $PK_Usuario);
    
        if($sentencia->execute()){
          $retorno = 200;
        }
        $sentencia->close();
          $conexion->close();
        return $retorno;
      }catch(mysqli_sql_exception $e){
        $conexion->close();
        return -1;
      }
    }

    public static function updateStock($PK_Producto, $Cantidad){
      try{
        include 'conexion.php';

        $retorno = 0;

        $query = "UPDATE producto SET stock = stock - ? WHERE PK_Producto = ?";
    
        $sentencia = $conexion->prepare($query);
        $sentencia->bind_param('ii', $Cantidad, $PK_Producto);
    
        if($sentencia->execute()){
          $retorno = 200;
        }
        
        $sentencia->close();
          $conexion->close();
        return $retorno;
      }catch(mysqli_sql_exception $e){
        $conexion->close();
        return -1;
      }
    }

    public static function sendBuy($PK_Carrito){
      try{
        include 'conexion.php';

        $retorno = 0;

        $query = "UPDATE carrito SET status = 2  WHERE PK_Carrito = ?";
    
        $sentencia = $conexion->prepare($query);
        $sentencia->bind_param('i', $PK_Carrito);
    
        if($sentencia->execute()){
          $retorno = 200;
        }
        
        $sentencia->close();
          $conexion->close();
        return $retorno;
      }catch(mysqli_sql_exception $e){
        $conexion->close();
        return -1;
      }
    }

    public static function getEmail($PK_Usuario){
      try{
          include 'conexion.php';
          $sentencia = $conexion->prepare("SELECT correo FROM usuario INNER JOIN comprador WHERE FK_Usuario = PK_Usuario AND PK_Comprador = '$PK_Usuario'");
          $sentencia->execute();

          $resultado = $sentencia->get_result();

          if($fila=$resultado->fetch_assoc()){
            $email = $fila["correo"];
          }

          $sentencia->close();
          $conexion->close();

          return $email;
      }catch(mysqli_sql_exception $e){
          $conexion->close();
          $response->PK_Usuario = -1;
          return $response;
      }
    }

    public static function getState($PK_Usuario){
      try{
          include 'conexion.php';
          $sentencia = $conexion->prepare("SELECT Nombre FROM entidad INNER JOIN comprador WHERE PK_Estado = FK_Estado AND PK_Comprador = $PK_Usuario");
          $sentencia->execute();

          $resultado = $sentencia->get_result();

          if($fila=$resultado->fetch_assoc()){
            $email = $fila["Nombre"];
          }

          $sentencia->close();
          $conexion->close();

          return $email;
      }catch(mysqli_sql_exception $e){
          $conexion->close();
          $response->PK_Usuario = -1;
          return $response;
      }
    }

    public static function getDomicilio($PK_Usuario){
      try{
          include 'conexion.php';
          $sentencia = $conexion->prepare("SELECT domicilio FROM comprador WHERE PK_Comprador = $PK_Usuario");
          $sentencia->execute();

          $resultado = $sentencia->get_result();

          if($fila=$resultado->fetch_assoc()){
            $email = $fila["domicilio"];
          }

          $sentencia->close();
          $conexion->close();

          return $email;
      }catch(mysqli_sql_exception $e){
          $conexion->close();
          $response->PK_Usuario = -1;
          return $response;
      }
    }
  }
?>