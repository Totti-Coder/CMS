<?php
session_start();
include '../incluir/conexion.php'; // Asegúrate de que $conexion es un objeto MySQLi

// --- Lógica de Redirección si ya hay sesión ---
if (isset($_SESSION['admin'])) {
    header("Location: gestion_productos.php");
    exit();
} elseif (isset($_SESSION['usuario'])) {
    header("Location: ../index.php"); 
    exit();
}

// --- Mostrar alerta si el usuario fue redirigido por no estar logueado (ej. desde pago.php) ---
$alerta_login = '';
if (isset($_SESSION['alerta_login'])) {
    $alerta_login = $_SESSION['alerta_login'];
    unset($_SESSION['alerta_login']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    // --- Sentencia Preparada para la seguridad ---
    $stmt = $conexion->prepare("SELECT rol FROM usuarios WHERE usuario = ? AND password = ?");

    if ($stmt === false) {
        $error = "Error interno del sistema.";
    } else {
        
        // Vincular los datos de forma segura (ambos son 's'trings)
        $stmt->bind_param("ss", $usuario, $password);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows == 1) {
            $fila = $resultado->fetch_assoc();
            $rol = $fila['rol'];

            // LÓGICA DE REDIRECCIÓN POR ROL
            if ($rol === 'admin') {
                $_SESSION['admin'] = $usuario; 
                header("Location: gestion_productos.php"); 
                exit();
            } else {
                $_SESSION['usuario'] = $usuario;
                header("Location: ../index.php"); 
                exit();
            }
        } else {
            $error = "Usuario o contraseña incorrectos.";
        }
        
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Estilos CSS simples para centrar y dar un toque visual */
        body {
            background-color: #f8f9fa; /* Fondo gris claro */
        }
        .login-card {
            margin-top: 100px;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0,0,0,.05); /* Sombra suave */
            border-radius: .5rem; /* Bordes redondeados */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-8 col-md-6 col-lg-6"> 
                <div class="card login-card">
                    <div class="card-body">
                        <h2 class="text-center mb-4">
                            <i class="fas fa-lock text-success"></i> Iniciar Sesión
                        </h2>
                        <?php 
                        if (isset($error)) { 
                            echo '<div class="alert alert-danger text-center">' . $error . '</div>'; 
                        } 
                        if ($alerta_login) { 
                            echo '<div class="alert alert-warning text-center">' . $alerta_login . '</div>'; 
                        }
                        ?>
                        
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="usuario">Usuario:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                    <input type="text" class="form-control" id="usuario" name="usuario" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="password">Contraseña:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    </div>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success btn-block mt-4">
                                <i class="fas fa-sign-in-alt"></i> Entrar
                            </button>
                            
                            <p class="text-center mt-3">
                                ¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>.
                            </p>
                            
                            <p class="text-center"><a href="../index.php">Volver a la tienda</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>