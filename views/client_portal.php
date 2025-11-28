<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'cliente') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Espacio - Atlantic City</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .gold-text { color: #d4af37; }
        .bg-dark-luxury { background-color: #1a1a1a; }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark-luxury">
        <div class="container">
            <a class="navbar-brand gold-text" href="#">Atlantic City</a>
            <span class="navbar-text text-white">
                Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?>
            </span>
            <a href="../auth/logout.php" class="btn btn-outline-warning btn-sm">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center p-5">
                <h1 class="display-4 mb-4">Bienvenido a tu Espacio Atlantic</h1>
                <p class="lead">Disfruta de tus beneficios exclusivos.</p>
            </div>
        </div>
    </div>
</body>
</html>
