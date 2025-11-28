<?php
require_once '../config/conexion.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

try {
    $conn = Conexion::conectar();

    if ($action == 'create' || $action == 'update') {
        $nombre = $_POST['nombre'];
        $direccion = $_POST['direccion'];
        $correo = $_POST['correo'];
        $telefono = $_POST['telefono'];
        $preferencias = $_POST['preferencias'];
        $segmento = $_POST['segmento'];
        // Default password for new users
        $password = '123456'; 

        if ($action == 'create') {
            $sql = "INSERT INTO Clientes (nombre, direccion, correo, telefono, preferencias, segmento, password) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$nombre, $direccion, $correo, $telefono, $preferencias, $segmento, $password]);
            echo json_encode(['success' => true, 'message' => 'Cliente creado correctamente']);
        } else {
            $id = $_POST['cliente_id'];
            $sql = "UPDATE Clientes SET nombre=?, direccion=?, correo=?, telefono=?, preferencias=?, segmento=? WHERE cliente_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$nombre, $direccion, $correo, $telefono, $preferencias, $segmento, $id]);
            echo json_encode(['success' => true, 'message' => 'Cliente actualizado correctamente']);
        }
    } elseif ($action == 'delete') {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM Clientes WHERE cliente_id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Cliente eliminado correctamente']);
    } elseif ($action == 'get_one') {
        $id = $_POST['id'];
        $stmt = $conn->prepare("SELECT * FROM Clientes WHERE cliente_id = ?");
        $stmt->execute([$id]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($cliente);
    } else {
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
