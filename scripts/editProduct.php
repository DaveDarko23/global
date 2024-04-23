<?php
  class Producto{
    public $imagen;
    public $name;
    public $descripcion;
    public $precio;
    public $stock;
    public $categoria;
    public $PK_Producto;

    public function createProduct($pImagen, $pName, $pDescripcion, $pPrecio, $pStock, $pCategoria, $pFK_Vendedor){
      $this->imagen = $pImagen;
      $this->name = $pName;
      $this->descripcion = $pDescripcion;
      $this->precio = $pPrecio;
      $this->stock = $pStock;
      $this->categoria = $pCategoria;
      $this->PK_Producto = $pFK_Vendedor;
    }
  }

  if(isset($_POST)){
    $name = $_POST["name"];
    $descripcion = $_POST["descripcion"];
    $precio = $_POST["precio"];
    $stock = $_POST["stock"];
    $categoria = $_POST["categoria"];
    $PK_Producto = $_POST["fk_vendedor"];
    

    //Recogemos el archivo enviado por el formulario
    $archivo = $_FILES['archivo']['name'];
    //Si el archivo contiene algo y es diferente de vacio
    if (isset($archivo) && $archivo !== "") {
       //Obtenemos algunos datos necesarios sobre el archivo
       $tipo = $_FILES['archivo']['type'];
       $tamano = $_FILES['archivo']['size'];
       $temp = $_FILES['archivo']['tmp_name'];
       //Se comprueba si el archivo a cargar es correcto observando su extensión y tamaño
       if (!((strpos($tipo, "jpeg") || strpos($tipo, "jpg") || strpos($tipo, "png")  || strpos($tipo, "webp")))) {
        echo  0;
        }
        else {
          //Si la imagen es correcta en tamaño y tipo
          //Se intenta subir al servidor
          $carpeta = '../../images/'.$PK_Producto;
          if(!file_exists($carpeta)){
            mkdir($carpeta, 0777, true);
          }
          
          if (move_uploaded_file($temp, $carpeta.'/'.$archivo)) {
            //Cambiamos los permisos del archivo a 777 para poder modificarlo posteriormente
            chmod($carpeta.'/'.$archivo, 0777);
            //Mostramos el mensaje de que se ha subido co éxito
            //Mostramos la imagen subida
            // echo '<p><img src="images/'.$archivo.'"></p>';
            $imagen = "http://10.0.0.3/images/".$PK_Producto.'/'.$archivo;
            $producto = new Producto();
            $producto->createProduct($imagen, $name, $descripcion, $precio,$stock,$categoria, $PK_Producto);
            include 'querys.php';
  
            Database::editProduct($producto);

            echo 200;
          }
          else {
            echo  -1;
        }
      }
    }else{
      include 'querys.php';
    $oldImage = $_POST["oldImage"];
      $producto = new Producto();
      $producto->createProduct($oldImage, $name, $descripcion, $precio,$stock,$categoria, $PK_Producto);
            
      Database::editProduct($producto);
      echo 200;
    }
  }
?>