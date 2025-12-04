<?php
require_once '../config/conexion.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

try {
    $conn = Conexion::conectar();

    if ($action == 'list') {
        $stmt = $conn->query("SELECT * FROM promociones ORDER BY fecha_inicio DESC");
        $promociones = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['data' => $promociones]);
    } elseif ($action == 'create' || $action == 'update') {
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $fecha_inicio = $_POST['fecha_inicio'];
        $fecha_fin = $_POST['fecha_fin'];

        if ($action == 'create') {
            $sql = "INSERT INTO promociones (nombre, descripcion, fecha_inicio, fecha_fin) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$nombre, $descripcion, $fecha_inicio, $fecha_fin]);
            echo json_encode(['success' => true, 'message' => 'Promoción creada correctamente']);
        } else {
            $id = $_POST['promocion_id'];
            $sql = "UPDATE promociones SET nombre=?, descripcion=?, fecha_inicio=?, fecha_fin=? WHERE promocion_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$nombre, $descripcion, $fecha_inicio, $fecha_fin, $id]);
            echo json_encode(['success' => true, 'message' => 'Promoción actualizada correctamente']);
        }
    } elseif ($action == 'delete') {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM promociones WHERE promocion_id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Promoción eliminada correctamente']);
    } elseif ($action == 'get_one') {
        $id = $_POST['id'];
        $stmt = $conn->prepare("SELECT * FROM promociones WHERE promocion_id = ?");
        $stmt->execute([$id]);
        $promocion = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($promocion);
    } else {
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
