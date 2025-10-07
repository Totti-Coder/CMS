<?php
// Incluir la conexión a la base de datos (asegúrate de que esta ruta sea correcta)
include 'incluir/conexion.php';

// Iniciar sesión para gestionar el carrito
session_start();
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Verificar si el usuario está logueado (como cliente o como administrador)
$es_admin = isset($_SESSION['admin']);
$es_usuario = isset($_SESSION['usuario']);
$esta_logueado = $es_admin || $es_usuario; 


// Inicializar la variable de mensaje de alerta para mostrar posteriormente
$mensaje_alerta = '';

// Procesar acciones del carrito
if (isset($_GET['accion']) && isset($_GET['id'])) {
    $id_producto = (int)$_GET['id'];
    $redireccionar_a_carrito = false; 

    if ($_GET['accion'] == 'agregar') {
        // Lógica para AGREGAR productos (solo 1 unidad inicial o se genera la alerta si ya existe uno con el mismo id)
        $encontrado = false;
        $cantidad_a_agregar = 1;
        
        // Busqueda dentro del carrito para comprobar si el producto ya se habia agregado -> & te permite editar la cantidad del producto sobre la marcha, actualizando $_SESSION['carrito']
        foreach ($_SESSION['carrito'] as &$item) {
            if ($item['id_producto'] == $id_producto) {
                // Producto encontrado: establece la alerta y NO suma cantidad
                $_SESSION['alerta_carrito'] = "El producto ya está en el carrito. Utiliza la tabla en el carrito para modificar la cantidad.";
                $encontrado = true;
                break;
            }
        }
        if (!$encontrado) {
            // Si no está en el carrito, lo añade con cantidad 1
            $_SESSION['carrito'][] = ['id_producto' => $id_producto, 'cantidad' => $cantidad_a_agregar];
        }
        
        // Redirecciona a la página de inicio para mostrar la alerta o confirmar la adición
        header('Location: index.php'); 
        exit;

    } elseif ($_GET['accion'] == 'eliminar') {
        // Lógica para ELIMINAR productos
        foreach ($_SESSION['carrito'] as $indice => $item) {
            if ($item['id_producto'] == $id_producto) {
                unset($_SESSION['carrito'][$indice]);
                break;
            }
        }
        // Se actualiza el array
        $_SESSION['carrito'] = array_values($_SESSION['carrito']);
        $redireccionar_a_carrito = true;

    } elseif ($_GET['accion'] == 'actualizar') {
        // Lógica para ACTUALIZAR la cantidad con validación de STOCK
        $nueva_cantidad_solicitada = (isset($_GET['cantidad']) && is_numeric($_GET['cantidad'])) ? (int)$_GET['cantidad'] : 1;
        $cantidad_final = $nueva_cantidad_solicitada;

        // 1. Obtener el stock disponible y el nombre del producto
        $consulta_stock = "SELECT stock, nombre FROM productos WHERE id_producto = " . $id_producto;
        $resultado_stock = $conexion->query($consulta_stock);
        
        if ($resultado_stock && $producto_db = $resultado_stock->fetch_assoc()) {
            $stock_disponible = (int)$producto_db['stock'];
            $nombre_producto = $producto_db['nombre'];

            // 2. Validación de stock
            if ($nueva_cantidad_solicitada > $stock_disponible) {
                // Si excede el stock, limitamos al máximo y guardamos alerta en la sesión
                $cantidad_final = $stock_disponible;
                $_SESSION['alerta_carrito'] = "Solo hay <strong> *{$stock_disponible} unidades* </strong> de <strong>" . htmlspecialchars($nombre_producto) . "</strong> en stock. La cantidad se ha ajustado al máximo disponible actualmente.";
            }

            // 3. Aplicar actualización o eliminación
            foreach ($_SESSION['carrito'] as $indice => &$item) {
                if ($item['id_producto'] == $id_producto) {
                    if ($cantidad_final > 0) {
                        // Actualiza la cantidad (solicitada o limitada por stock)
                        $item['cantidad'] = $cantidad_final;
                    } else {
                        // Si la cantidad es 0 o menor, se elimina el producto del carrito
                        unset($_SESSION['carrito'][$indice]);
                    }
                    break;
                }
            }
            
            // Si se eliminó un producto (cantidad <= 0), reindexamos
            if ($cantidad_final <= 0) {
                 $_SESSION['carrito'] = array_values($_SESSION['carrito']);
            }
        } else {
             // Manejo de error si el producto no se encuentra en la base de datos
             $_SESSION['alerta_carrito'] = "Error: Producto no encontrado en la base de datos.";
        }
        
        $redireccionar_a_carrito = true;
    }

    // Redireccionar al propio carrito para las acciones de manejo interno (actualizar, eliminar)
    if ($redireccionar_a_carrito) {
        header('Location: carrito.php');
        exit;
    }
}

// Mostrar la alerta si existe en la sesión y luego limpiarla
if (isset($_SESSION['alerta_carrito'])) {
    $mensaje_alerta = $_SESSION['alerta_carrito'];
    unset($_SESSION['alerta_carrito']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="recursos/css/estilos.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php include 'incluir/encabezado.php'; ?>
    <div class="container mt-5">
        
        <?php 
        // Mostrar la alerta generada por la acción 'actualizar' (o 'agregar' si vino de index.php)
        if ($mensaje_alerta): 
        ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Advertencia:</strong> <?php echo $mensaje_alerta; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <h1 class="text-center mb-4">Mi Carrito</h1>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Productos</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Subtotal</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    if (!empty($_SESSION['carrito'])) {
                        foreach ($_SESSION['carrito'] as $item) {
                            // Consulta para obtener los datos del producto
                            // NOTA: Se recomienda usar Sentencias Preparadas aquí.
                            $consulta = "SELECT * FROM productos WHERE id_producto = " . $item['id_producto'];
                            $resultado = $conexion->query($consulta);
                            
                            if ($resultado && $resultado->num_rows > 0) {
                                $producto = $resultado->fetch_assoc();
                                $subtotal = $producto['precio'] * $item['cantidad'];
                                $total += $subtotal;
                                
                                echo '
                                <tr>
                                    <td>' . htmlspecialchars($producto['nombre']) . '</td>
                                    <td>
                                        <form method="GET" action="carrito.php" class="form-inline">
                                            <input type="hidden" name="accion" value="actualizar">
                                            <input type="hidden" name="id" value="' . $item['id_producto'] . '">
                                            <input type="number" name="cantidad" value="' . $item['cantidad'] . '" min="0" class="form-control form-control-sm mr-2" style="width: 70px;">
                                            <button type="submit" class="btn btn-info btn-sm">Actualizar</button>
                                        </form>
                                    </td>
                                    <td>$' . number_format($producto['precio'], 2) . '</td>
                                    <td>$' . number_format($subtotal, 2) . '</td>
                                    <td>
                                        <a href="carrito.php?accion=eliminar&id=' . $item['id_producto'] . '" class="btn btn-danger btn-sm">Eliminar</a>
                                    </td>
                                </tr>
                                ';
                            }
                        }
                    } else {
                        echo '<tr><td colspan="5" class="text-center">El carrito está vacío.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <h3 class="text-right">Total: $<?php echo number_format($total, 2); ?></h3>
        
        <div class="text-right mt-3">
            <?php if ($esta_logueado && $total > 0): ?>
                <a href="pago.php" class="btn btn-success btn-lg">
                    Proceder al Pago
                </a>
            <?php elseif ($total > 0): ?>
                <a href="admin/inicio_sesion.php" class="btn btn-warning btn-lg">
                    Iniciar Sesión para Pagar
                </a>
            <?php else: ?>
                <button type="button" class="btn btn-secondary btn-lg" disabled>
                    Carrito Vacío
                </button>
            <?php endif; ?>
        </div>
    </div>
    <?php include 'incluir/pie.php'; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>