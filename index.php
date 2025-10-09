<?php
// Incluir la conexión a la base de datos
include 'incluir/conexion.php';


session_start(); 

// Verifica si hay un mensaje de alerta en la sesión
$mensaje_alerta = '';
if (isset($_SESSION['alerta_carrito'])) {
    $mensaje_alerta = $_SESSION['alerta_carrito'];
    unset($_SESSION['alerta_carrito']); // Borra el mensaje para que no se muestre al recargar
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Fruteria</title>
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    </head>
<body>
    <?php include 'incluir/encabezado.php'; ?>

    <div class="container mt-5">
        
        <?php 
        //Muestra la alerta de Bootstrap
        if ($mensaje_alerta): 
        ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Atención:</strong> <?php echo htmlspecialchars($mensaje_alerta); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
        
        <h1 class="text-center mb-5">Bienvenido a la fruteria de Pablo</h1>
        <div class="row">
            <?php
            // Consultar productos de la base de datos
            $consulta = "SELECT * FROM productos";
            $resultado = $conexion-> query($consulta);
            // Si hay productos en la tabla procede a mostrarlos
            if ($resultado-> num_rows > 0) {
                while ($producto = $resultado->fetch_assoc()) {
                    $ruta_imagen = 'recursos/imagenes/' . $producto['imagen'];

                    echo '
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm rounded-lg"> <a href="' . $ruta_imagen . '" target="_blank">
                                <div class="card-img-top" style="height: 200px; overflow: hidden; border-top-left-radius: .5rem; border-top-right-radius: .5rem;">
                                    <img src="' . $ruta_imagen . '" class="img-fluid" alt="' . htmlspecialchars($producto['nombre']) . '" style="width: 100%; object-fit: cover; height: 100%;">
                                </div>
                            </a>
                            <div class="card-body">
                                <h5 class="card-title">' . htmlspecialchars($producto['nombre']) . '</h5>
                                <p class="card-text">' . htmlspecialchars($producto['descripcion']) . '</p>
                                <p class="card-text"><strong>Precio: $' . number_format($producto['precio'], 2) . '</strong></p> 
                                <a href="carrito.php?accion=agregar&id=' . $producto['id_producto'] . '" class="btn btn-success btn-block">
                                    <i class="fas fa-cart-plus"></i> Añadir al carrito
                                </a>
                            </div>
                        </div>
                    </div>
                    ';
                }
            } else {
                echo '<p class="text-center">No tenemos este producto disponible actualmente.</p>';
            }
            ?>
        </div>
    </div>

    <?php include 'incluir/pie.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>