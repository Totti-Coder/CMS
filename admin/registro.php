<?php
session_start();
include '../incluir/conexion.php'; // Asegúrate de que esta ruta sea correcta

// --- Lógica de Redirección si ya hay sesión ---
// Si ya está logueado (admin o cliente), lo enviamos al índice.
if (isset($_SESSION['admin']) || isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit();
}

$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Obtener y sanear datos
    $usuario = trim($_POST['usuario']);
    $password = $_POST['password'];
    // CLAVE: Rol fijo de 'cliente' para coincidir con tu BD
    $rol = 'cliente'; 

    // 2. Validación de datos
    if (empty($usuario) || empty($password)) {
        $error = "Por favor, rellena todos los campos.";
    } elseif (strlen($usuario) < 3) {
        $error = "El nombre de usuario debe tener al menos 3 caracteres.";
    } elseif (strlen($password) < 6) {
        $error = "La contraseña debe tener al menos 6 caracteres.";
    } else {
        // 3. Verificar si el usuario ya existe
        // Usa sentencias preparadas para prevenir inyección SQL (ya lo haces, ¡bien!)
        $stmt_check = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE usuario = ?");
        $stmt_check->bind_param("s", $usuario);
        $stmt_check->execute();
        $stmt_check->store_result();
        
        if ($stmt_check->num_rows > 0) {
            $error = "El nombre de usuario ya está en uso. Por favor, elige otro.";
        } else {
            
            // 🔑 CLAVE DE SEGURIDAD: Hashear la contraseña
            $password_cifrada = password_hash($password, PASSWORD_DEFAULT);
            
            // 4. Insertar nuevo usuario (usando la contraseña cifrada)
            $stmt_insert = $conexion->prepare("INSERT INTO usuarios (usuario, password, rol) VALUES (?, ?, ?)");
            
            if ($stmt_insert === false) {
                $error = "Error interno del sistema al preparar el registro.";
            } else {
                // Insertamos la contraseña cifrada ($password_cifrada)
                $stmt_insert->bind_param("sss", $usuario, $password_cifrada, $rol);
                
                if ($stmt_insert->execute()) {
                    // Registro exitoso: Iniciar sesión automáticamente y redirigir
                    $_SESSION['usuario'] = $usuario;
                    $exito = "¡Registro completado con éxito! Serás redirigido a la tienda.";
                    
                    // Redirigir después de un pequeño retraso para mostrar el mensaje de éxito
                    header("Refresh: 2; url=../index.php"); 
                    exit();
                } else {
                    $error = "Error al intentar registrar el usuario: " . $stmt_insert->error;
                }
                $stmt_insert->close();
            }
        }
        $stmt_check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Cliente</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .register-card { 
            margin-top: 100px;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0,0,0,.05);
            border-radius: .5rem; 
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-8 col-md-6 col-lg-6"> 
                <div class="card register-card">
                    <div class="card-body">
                        <h2 class="text-center mb-4">
                            <i class="fas fa-user-plus text-success"></i> Crear Cuenta
                        </h2>
                        <?php 
                        if ($error) { 
                            echo '<div class="alert alert-danger text-center">' . $error . '</div>'; 
                        }
                        if ($exito) {
                            echo '<div class="alert alert-success text-center">' . $exito . '</div>'; 
                        }
                        ?>
                        
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="usuario">Nombre de Usuario:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                    <input type="text" class="form-control" id="usuario" name="usuario" required 
                                           value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>">
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
                                <i class="fas fa-check-circle"></i> Registrarme
                            </button>
                            
                            <p class="text-center mt-3">
                                ¿Ya tienes una cuenta? <a href="inicio_sesion.php">Inicia Sesión</a>
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