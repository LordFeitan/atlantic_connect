<?php
session_start();
require_once '../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $tipo_usuario = $_POST['tipo_usuario'];

    try {
        $conn = Conexion::conectar();
        
        if ($tipo_usuario == 'cliente') {
            $stmt = $conn->prepare("SELECT cliente_id as id, nombre, correo, password FROM clientes WHERE correo = :email");
        } else {
            $stmt = $conn->prepare("SELECT empleado_id as id, nombre, correo, password FROM empleados WHERE correo = :email");
        }

        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Secure password check using password_verify
        if ($user && password_verify($password, $user['password'])) {
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);
            
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['tipo_usuario'] = $tipo_usuario;

            if ($tipo_usuario == 'cliente') {
                header("Location: ../views/client_portal.php");
                // echo "Bienvenido Cliente";
            } else {
                header("Location: ../views/admin/admin_dashboard.php");
            }
            exit();
        } else {
            header("Location: ../views/public/login.php?error=1");
            exit();
        }

    } catch (PDOException $e) {
        // In production, log error instead of showing it
        header("Location: ../views/public/login.php?error=1");
        exit();
    }
} else {
    header("Location: ../views/public/login.php");
    exit();
}
?>
