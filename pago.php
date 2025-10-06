<?php
// Incluir la conexión a la base de datos
include 'incluir/conexion.php';

// Iniciar sesión para acceder al carrito
session_start();

// Verificar si el carrito tiene productos. Si está vacío, redirige al carrito.
if (empty($_SESSION['carrito'])) {
    header("Location: carrito.php");
    exit();
}

$total_a_pagar = 0;
$error_stock = false;

// Bucle para calcular el total y verificar el stock antes de que el usuario haga click.
foreach ($_SESSION['carrito'] as $item) {
    // Consulta segura para obtener datos del producto
    $consulta = "SELECT precio, stock, nombre FROM productos WHERE id_producto = " . $item['id_producto'];
    $resultado = $conexion->query($consulta);

    if ($resultado && $resultado->num_rows > 0) {
        $producto = $resultado->fetch_assoc();
        
        // VALIDACIÓN DEL STOCK
        if ($item['cantidad'] > $producto['stock']) {
            // Si la cantidad en el carrito excede el stock, marca error y redirige.
            $_SESSION['alerta_carrito'] = "Lo sentimos: No hay suficientes unidades de <strong>" . htmlspecialchars($producto['nombre']) . "</strong> disponibles. Por favor, ajusta la cantidad en el carrito.";
            header("Location: carrito.php");
            exit();
        }
        
        // Calcular total solo si hay stock suficiente
        $total_a_pagar += $producto['precio'] * $item['cantidad'];
    }
}

// LÓGICA DE PROCESAMIENTO DEL PAGO (Se ejecuta al presionar "Completar Compra")
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Limpiar el carrito después del pago exitoso
    unset($_SESSION['carrito']);
    
    // Redirigir a la página de confirmación
    header("Location: pago_exitoso.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago</title>
    
    <link rel="stylesheet" href="recursos/css/estilos.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include 'incluir/encabezado.php'; ?>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-10">
                
                <div class="card shadow-lg mb-5 border-0 rounded-lg">
                    
                    <div class="card-header bg-success text-white text-center py-3 rounded-top-lg">
                        <h3 class="mb-0 font-weight-normal">Confirmación de la Orden</h3>
                    </div>
                    
                    <div class="card-body p-5 bg-white rounded-0"> 
                        
                        <div class="alert alert-light text-center border p-3 mb-4 rounded">
                            <p class="mb-0 text-secondary">
                                Los datos de pago (simulados) se procesarán al completar la compra.
                            </p>
                        </div>

                        <hr class="my-4">
                        
                        <div class="row align-items-center">
                            <div class="col-6 text-left">
                                <p class="h4 font-weight-bold mb-0 text-primary">TOTAL FINAL:</p>
                                <p class="text-muted small">Monto total de los productos en tu carrito.</p>
                            </div>
                            <div class="col-6 text-right">
                                <span class="text-success display-3 font-weight-bolder">
                                    $<?php echo number_format($total_a_pagar, 2); ?>
                                </span>
                            </div>
                        </div>
                        
                    </div>
                    
                    <div class="card-footer bg-light border-0 pt-4 pb-4 rounded-bottom-lg">
                        <form method="POST" action="pago.php">
                            <div class="d-flex justify-content-between">
                                
                                <a href="carrito.php" class="btn btn-outline-secondary btn-lg flex-fill mr-2">
                                    <i class="fas fa-arrow-left"></i> Modificar Carrito
                                </a>
                                
                                <button type="submit" class="btn btn-success btn-lg flex-fill shadow-lg">
                                    <i class="fas fa-money-check-alt"></i> Pagar y Finalizar
                                </button>
                                
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <?php include 'incluir/pie.php'; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>