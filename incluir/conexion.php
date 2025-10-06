<?php
// Este archivo carga las credenciales de forma segura desde config.php

// Definir la RUTA ABSOLUTA al archivo config.php
$ruta_config = __DIR__ . '/../../config.php'; 

// Cargar la configuración.
if (file_exists($ruta_config)) {
    // La función require devuelve el array definido en config.php
    $config = require $ruta_config;
} else {
    // Si el archivo no existe, detiene la ejecución con un mensaje de error
    die("Error de configuración: El archivo de credenciales no fue encontrado.");
}

// Asignar las variables desde el array de configuración
$host = $config['host'];
$user = $config['user'];
$passwd = $config['passwd'];
$base_de_datos = $config['base_de_datos'];

// Creo la conexion a la base de datos
$conexion = new mysqli($host, $user, $passwd, $base_de_datos);

// Verifico la conexión
if ($conexion-> connect_error) {
    die("Se ha producido un error de conexión a la base de datos: " . $conexion->connect_error);
}

// Establecer el conjunto de caracteres a UTF-8
$conexion-> set_charset("utf8");

// NOTA: Se elimina el 'echo "";' ya que no es necesario y puede causar errores de encabezado.
?>