<?php
session_start();
// Verificar si el administrador ha iniciado sesión
if (!isset($_SESSION['admin'])) {
    header("Location: inicio_sesion.php");
    exit();
}
// Incluir la conexión a la base de datos
include '../incluir/conexion.php';
// Manejar el envío del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    // Manejo de la imagen con un array especial de PHP $_FILES
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        //Coge la ruta completa de la imagen y nos muestra unicamente el nombre
        $nombre_imagen = basename($_FILES['imagen']['name']);
        $ruta_destino = '../recursos/imagenes/' . $nombre_imagen;
        // Mover la imagen al directorio de destino
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
            // Guardar la información en la base de datos
            $consulta = "INSERT INTO productos (nombre, descripcion, precio, imagen, stock) VALUES ('$nombre', '$descripcion', $precio, '$nombre_imagen', $stock)";
            if ($conexion->query($consulta)) {
                $mensaje = "Producto agregado con éxito.";
                header("Location: gestion_productos.php?mensaje=" . urlencode($mensaje));
                exit();
            } else {
                $error = "Error al guardar el producto: " . $conexion->error;
            }
        } else {
            $error = "Error al subir la imagen.";
        }
    } else {
        $error = "No se ha seleccionado una imagen o hubo un error al subirla.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Agregar Nuevo Producto</h2>
        <?php if (isset($error)) { echo '<div class="alert alert-danger">' . $error . '</div>'; } ?>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nombre">Nombre del producto:</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea class="form-control" id="descripcion" name="descripcion" required></textarea>
            </div>
            <div class="form-group">
                <label for="precio">Precio:</label>
                <input type="number" step="0.01" class="form-control" id="precio" name="precio" required>
            </div>
            <div class="form-group">
                <label for="stock">Stock:</label>
                <input type="number" class="form-control" id="stock" name="stock" required>
            </div>
            <div class="form-group">
                <label for="imagen">Imagen:</label>
                <input type="file" class="form-control-file" id="imagen" name="imagen" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-success">Agregar Producto</button>
            <a href="gestion_productos.php" class="btn btn-secondary">Volver</a>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>