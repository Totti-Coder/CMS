<?php
session_start();
include '../incluir/conexion.php';

// Lógica de Redirección si ya hay sesión 
if (isset($_SESSION['admin'])) {
    header("Location: gestion_productos.php");
    exit();
} elseif (isset($_SESSION['usuario'])) {
    header("Location: ../index.php"); 
    exit();
}

// Mostrar alerta si el usuario fue redirigido por no estar logueado.
$alerta_login = '';
if (isset($_SESSION['alerta_login'])) {
    $alerta_login = $_SESSION['alerta_login'];
    unset($_SESSION['alerta_login']);
}

$error = ''; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = trim($_POST['usuario']); 
    $password_ingresada = $_POST['password']; 

    // Setencia preparada que busca el usuario y recupera el hash y el rol
    $stmt = $conexion->prepare("SELECT password, rol FROM usuarios WHERE usuario = ?");

    if ($stmt === false) {
        $error = "Error interno del sistema al preparar la consulta.";
    } else {
        
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows == 1) {
            $fila = $resultado->fetch_assoc();
            $hash_almacenado = $fila['password']; 
            $rol = $fila['rol'];

            // Usar password_verify() para comparar la contraseña ingresada
            // con el hash almacenado.
            if (password_verify($password_ingresada, $hash_almacenado)) {
                
                // Contraseña correcta: Iniciar sesión y redirigir
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
                // Contraseña no coincide con el hash
                $error = "Usuario o contraseña incorrectos."; 
            }
        } else {
            // Usuario no encontrado
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
        body {
            background-color: #f8f9fa; 
        }
        .login-card {
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
                <div class="card login-card">
                    <div class="card-body">
                        <h2 class="text-center mb-4">
                            <i class="fas fa-lock text-success"></i> Iniciar Sesión
                        </h2>
                       <?php 
						// Verifica que $error tenga CONTENIDO (no solo que exista)
						if ($error) { 
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
