<?php
session_start();

// If user is not logged in, redirect to login page
if (!isset($_SESSION['usuario_id'])) {
    header("Location: views/login.php");
    exit();
}

// If user is logged in, redirect to their respective dashboard
if ($_SESSION['tipo_usuario'] == 'cliente') {
    header("Location: views/client_portal.php");
} else {
    header("Location: views/admin_dashboard.php");
}
exit();
?>
