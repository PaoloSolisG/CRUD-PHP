<?php

$host="localhost";
$bd="sitio_web";
$usuario="root";
$contrasenia="";

try {
    $conexion = new PDO("mysql:host=$host;dbname=$bd;charset=utf8", $usuario, $contrasenia);
   
} catch (Exception $ex) {
    echo $ex->getMessage();
}


?>