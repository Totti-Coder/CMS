<?php
// Control de acceso
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: inicio_sesion.php");
    exit();
}
include '../incluir/conexion.php';

// Eliminar productos
if (isset($_GET['accion']) && $_GET['accion'] == 'eliminar' && isset($_GET['id'])) {
    $id_producto = (int)$_GET['id'];
    $conexion->query("DELETE FROM productos WHERE id_producto = $id_producto");
    header("Location: gestion_productos.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Gestión de Productos</h2>
        <div class="text-right mb-3">
            <a href="agregar_producto.php" class="btn btn-success">Agregar Producto</a>
            <a href="cerrar_sesion.php" class="btn btn-danger">Cerrar Sesión</a>
        </div>
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Consulta todos los productos de la base de datos
                $consulta = "SELECT * FROM productos";
                $resultado = $conexion->query($consulta);
                if ($resultado->num_rows > 0) {
                    while ($producto = $resultado->fetch_assoc()) {
                        echo '
                        <tr>
                            <td>' . $producto['id_producto'] . '</td>
                            <td>' . $producto['nombre'] . '</td>
                            <td>' . $producto['descripcion'] . '</td>
                            <td>$' . number_format($producto['precio'], 2) . '</td>
                            <td>' . $producto['stock'] . '</td>
                            <td>
                                <a href="editar_producto.php?id=' . $producto['id_producto'] . '" class="btn btn-warning btn-sm">Editar</a>
                                <a href="gestion_productos.php?accion=eliminar&id=' . $producto['id_producto'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'¿Estás seguro de que quieres eliminar este producto?\')">Eliminar</a>
                            </td>
                        </tr>
                        ';
                    }
                } else {
                    echo '<tr><td colspan="6" class="text-center">No hay productos disponibles.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>