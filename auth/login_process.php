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
            // Corrected columns: cliente_id, nombre, correo (not email), password. Removed apellido.
            $stmt = $conn->prepare("SELECT cliente_id as id, nombre, correo, password FROM Clientes WHERE correo = :email");
        } else {
            // Corrected columns: empleado_id, nombre, correo (not email), password. Removed apellido.
            $stmt = $conn->prepare("SELECT empleado_id as id, nombre, correo, password FROM Empleados WHERE correo = :email");
        }

        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Simple password check (plaintext)
        if ($user && $password === $user['password']) {
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['tipo_usuario'] = $tipo_usuario;

            if ($tipo_usuario == 'cliente') {
                header("Location: ../views/client_portal.php");
            } else {
                header("Location: ../views/admin_dashboard.php");
            }
            exit();
        } else {
            // Debugging: Uncomment to see why it failed if needed
            // echo "User found: " . ($user ? 'Yes' : 'No') . "<br>";
            // echo "Password match: " . ($user && $password === $user['password'] ? 'Yes' : 'No');
            // exit();
            
            header("Location: ../views/login.php?error=1");
            exit();
        }

    } catch (PDOException $e) {
        // In production, log error instead of showing it
        header("Location: ../views/login.php?error=1");
        exit();
    }
} else {
    header("Location: ../views/login.php");
    exit();
}
?>
