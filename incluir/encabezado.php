<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Variables clave para la lógica condicional
$es_admin = isset($_SESSION['admin']);
$es_usuario = isset($_SESSION['usuario']);
$esta_logueado = $es_admin || $es_usuario;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Fruteria de Pablo </title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-apple" style="font-size: 1.5rem; color: #5cb85c;"></i> Fruteria
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item mr-3">
                        <a class="nav-link" href="index.php">
                            <i class="bi bi-house"></i> Inicio
                        </a>
                    </li>
                    <li class="nav-item mr-3">
                        <a class="nav-link" href="carrito.php">
                            <i class="bi bi-cart"></i> Carrito
                        </a>
                    </li>
                    <li class="nav-item mr-3">
                        <a class="nav-link" href="pago.php">
                            <i class="bi bi-wallet"></i> Pagar
                        </a>
                    </li>
                    <li class="nav-item mr-3">
                        <a class="nav-link" href="admin/registro.php">
                            <i class="bi bi-person-plus"></i> Registro
                        </a>
                    </li>
                    <?php if ($es_admin): ?>
                        <li class="nav-item mr-3">
                            <a class="nav-link text-primary font-weight-bold" href="admin/gestion_productos.php">
                                <i class="bi bi-person-badge"></i> Panel Admin
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if ($esta_logueado): ?>
                        <li class="nav-item">
                            <a class="nav-link text-danger" href="admin/cerrar_sesion.php">
                                <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="admin/inicio_sesion.php">
                                <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                            </a>
                        </li>
                    <?php endif; ?>

                </ul>
            </div>
        </div>
    </nav>
</body>

</html>