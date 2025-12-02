<?php
session_start();
require_once '../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $dni = trim($_POST['dni']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $direccion = trim($_POST['direccion']);
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validar contraseñas
    if ($password !== $confirm_password) {
        header("Location: ../views/public/register.php?error=password");
        exit();
    }

    try {
        $conn = Conexion::conectar();

        // Verificar si el DNI ya existe
        $stmt = $conn->prepare("SELECT cliente_id FROM clientes WHERE dni = :dni");
        $stmt->execute([':dni' => $dni]);
        if ($stmt->rowCount() > 0) {
            header("Location: ../views/public/register.php?error=dni_exists");
            exit();
        }

        // Verificar si el correo ya existe
        $stmt = $conn->prepare("SELECT cliente_id FROM clientes WHERE correo = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->rowCount() > 0) {
            header("Location: ../views/public/register.php?error=email_exists");
            exit();
        }

        // Hash del password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Obtener ID del segmento 'Nuevo'
        $stmtSeg = $conn->prepare("SELECT segmento_id FROM Segmentos WHERE nombre = 'Nuevo'");
        $stmtSeg->execute();
        $segmento_id = $stmtSeg->fetchColumn();
        if (!$segmento_id) $segmento_id = 1; // Fallback

        // Insertar nuevo cliente
        $sql = "INSERT INTO clientes (nombre, dni, fecha_nacimiento, direccion, telefono, correo, password, segmento_id, fecha_registro) VALUES (:nombre, :dni, :fecha, :direccion, :telefono, :email, :pass, :seg_id, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':dni', $dni);
        $stmt->bindParam(':fecha', $fecha_nacimiento);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':pass', $hashed_password);
        $stmt->bindParam(':seg_id', $segmento_id);
        
        if ($stmt->execute()) {
            // Auto-login o redirigir a login
            // Vamos a redirigir a login con mensaje de éxito (podríamos hacer auto-login también)
            header("Location: ../views/public/login.php?registered=1");
            exit();
        } else {
            header("Location: ../views/public/register.php?error=db");
            exit();
        }

    } catch (PDOException $e) {
        // Log error
        header("Location: ../views/public/register.php?error=db");
        exit();
    }
} else {
    header("Location: ../views/public/register.php");
    exit();
}
?>
