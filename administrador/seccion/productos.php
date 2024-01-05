<?php include("../template/cabecera.php") ?>
<?php


$txtID = (isset($_POST['txtID'])) ? $_POST['txtID'] : "";
$txtNombre = (isset($_POST['txtNombre'])) ? $_POST['txtNombre'] : "";
$txtImagen = (isset($_FILES['txtImagen']['name'])) ? $_FILES['txtImagen']['name'] : "";
$accion = (isset($_POST['accion'])) ? $_POST['accion'] : "";


include("../config/bd.php");


switch ($accion) {
    case "Agregar":

        $sentenciaSQL = $conexion->prepare("INSERT INTO libros (nombre,imagen) VALUES (:nombre,:imagen);");
        $sentenciaSQL->bindParam(':nombre', $txtNombre);

        $fecha = new DateTime();
        $nombreArchivo = ($txtImagen != "") ? $fecha->getTimestamp() . "_" . $_FILES["txtImagen"]["name"] : "imagen.jpg";

        $tmpImagen = $_FILES["txtImagen"]["tmp_name"];

        if ($tmpImagen != "") {
            move_uploaded_file($tmpImagen, "../../img/" . $nombreArchivo);
        }

        $sentenciaSQL->bindParam(':imagen', $nombreArchivo);
        $sentenciaSQL->execute();
        header("Location:productos.php");
        break;
    case "Modificar":



        $sentenciaSQL = $conexion->prepare("UPDATE  libros SET nombre=:nombre WHERE id=:id");
        $sentenciaSQL->bindParam(':nombre', $txtNombre);
        $sentenciaSQL->bindParam(':id', $txtID);
        $sentenciaSQL->execute();

        if ($txtImagen != "") {

            $fecha = new DateTime();
            $nombreArchivo = ($txtImagen != "") ? $fecha->getTimestamp() . "_" . $_FILES["txtImagen"]["name"] : "imagen.jpg";
            $tmpImagen = $_FILES["txtImagen"]["tmp_name"];

            move_uploaded_file($tmpImagen, "../../img/" . $nombreArchivo);

            $sentenciaSQL = $conexion->prepare("SELECT imagen  FROM libros WHERE id=:id");
            $sentenciaSQL->bindParam(':id', $txtID);
            $sentenciaSQL->execute();
            $libro = $sentenciaSQL->fetch(PDO::FETCH_LAZY);


            if (isset($libro["imagen"]) && ($libro["imagen"] != "imagen.jpg")) {
                if (file_exists("../../img/" . $libro["imagen"])) {
                    unlink("../../img/" . $libro["imagen"]);
                }
            }

            $sentenciaSQL = $conexion->prepare("UPDATE  libros SET imagen=:imagen WHERE id=:id");
            $sentenciaSQL->bindParam(':imagen', $nombreArchivo);
            $sentenciaSQL->bindParam(':id', $txtID);
            $sentenciaSQL->execute();
        }

        header("Location:productos.php");
        break;
    case "Cancelar":
        
        header("Location:productos.php");
        break;
    case "Seleccionar":
        //echo "presionado botom Seleccionar";

        $sentenciaSQL = $conexion->prepare("SELECT * FROM libros WHERE id=:id");
        $sentenciaSQL->bindParam(':id', $txtID);
        $sentenciaSQL->execute();
        $libro = $sentenciaSQL->fetch(PDO::FETCH_LAZY);

        $txtNombre = $libro['nombre'];
        $txtImagen = $libro['imagen'];
        break;
    case "Borrar":
        //echo "presionado botom Borrar";
        $sentenciaSQL = $conexion->prepare("SELECT imagen  FROM libros WHERE id=:id");
        $sentenciaSQL->bindParam(':id', $txtID);
        $sentenciaSQL->execute();
        $libro = $sentenciaSQL->fetch(PDO::FETCH_LAZY);


        if (isset($libro["imagen"]) && ($libro["imagen"] != "imagen.jpg")) {
            if (file_exists("../../img/" . $libro["imagen"])) {
                unlink("../../img/" . $libro["imagen"]);
            }
        }


        $sentenciaSQL = $conexion->prepare("DELETE FROM libros WHERE id=:id");
        $sentenciaSQL->bindParam(':id', $txtID);
        $sentenciaSQL->execute();
        header("Location:productos.php");
        break;
}

$sentenciaSQL = $conexion->prepare("SELECT * FROM libros");
$sentenciaSQL->execute();
$listaLibros = $sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);


?>
<div class="col-md-5">

    <div class="card">
        <div class="card-header">
            Datos de Libros
        </div>

        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">

                <div class="form-group">
                    <label for="txtID">ID</label>
                    <input name="txtID" required readonly value="<?php echo $txtID ?>" id="txtID" type="text" class="form-control" placeholder="ID">
                </div>

                <div class="form-group">
                    <label for="txtNombre">Nombre:</label>
                    <input required name="txtNombre" id="txtNombre" value="<?php echo $txtNombre; ?>" type="text" class="form-control" placeholder="Nombre el libro">
                </div>

                <div class="form-group">
                    <label for="txtImagen">Imagen:</label>
     
                   <br>

                    <?php if ($txtImagen != "") { ?>
                        <img class="img-thumbnail rounded" src="../../img/<?php echo $txtImagen ?>" width="60" alt="">

                    <?php }  ?>

                    <input name="txtImagen" id="txtImagen" type="file" value="<?php echo $txtImagen; ?>" class="form-control" placeholder="Nombre el libro">
                </div>


                <div class="btn-group" role="group" aria-label="">
                    <button type="submit" name="accion" <?php echo($accion =="Seleccionar")?"disabled":""; ?> value="Agregar" class="btn btn-success">Agregar</button>
                    <button type="submit" name="accion" <?php echo($accion != "Seleccionar")?"disabled":""; ?> value="Modificar" class="btn btn-warning">Modificar</button>
                    <button type="submit" name="accion" <?php echo($accion != "Seleccionar")?"disabled":""; ?> value="Cancelar" class="btn btn-info">Cancelar</button>
                </div>
            </form>
        </div>


    </div>



</div>
<div class="col-md-7">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Imagen</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($listaLibros as $libro) { ?>

                <tr>
                    <td><?php echo $libro['id'] ?></td>
                    <td><?php echo $libro['nombre'] ?></td>
                    <td>
                        <img class="img-thumbnail rounded" src="../../img/<?php echo $libro['imagen'] ?>" width="60" alt="">


                    </td>
                    <td>
                        Seleccionar | Borrar
                        <form method="post">
                            <input type="hidden" name="txtID" id="txtID" value="<?php echo $libro['id'] ?>" />
                            <input type="submit" name="accion" value="Seleccionar" class="btn btn-primary" />
                            <input type="submit" name="accion" value="Borrar" class="btn btn-danger" />
                        </form>
                    </td>
                </tr>
            <?php  } ?>
        </tbody>
    </table>
</div>


<?php include("../template/pie.php") ?>