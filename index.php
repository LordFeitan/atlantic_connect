<?php
session_start();

// If user is not logged in, redirect to login page
if (!isset($_SESSION['usuario_id'])) {
    header("Location: views/public/login.php");
    exit();
}

// If user is logged in, redirect to their respective dashboard
if ($_SESSION['tipo_usuario'] == 'cliente') {
    // Redirigir a portal cliente (To be implemented)
    echo "Bienvenido Cliente";
} else {
    header("Location: views/admin/admin_dashboard.php");
}
exit();
?>
