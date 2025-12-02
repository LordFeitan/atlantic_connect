<?php
require_once '../config/conexion.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

try {
    $conn = Conexion::conectar();

    if ($action == 'list') {
        $stmt = $conn->query("SELECT * FROM Segmentos");
        $segmentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['data' => $segmentos]);
    } elseif ($action == 'create' || $action == 'update') {
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $criterios = $_POST['criterios_minimos'];

        if ($action == 'create') {
            $sql = "INSERT INTO Segmentos (nombre, descripcion, criterios_minimos) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$nombre, $descripcion, $criterios]);
            echo json_encode(['success' => true, 'message' => 'Segmento creado correctamente']);
        } else {
            $id = $_POST['segmento_id'];
            $sql = "UPDATE Segmentos SET nombre=?, descripcion=?, criterios_minimos=? WHERE segmento_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$nombre, $descripcion, $criterios, $id]);
            echo json_encode(['success' => true, 'message' => 'Segmento actualizado correctamente']);
        }
    } elseif ($action == 'delete') {
        $id = $_POST['id'];
        // Check if used by clients
        $check = $conn->prepare("SELECT COUNT(*) FROM Clientes WHERE segmento_id = ?");
        $check->execute([$id]);
        if ($check->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'No se puede eliminar: Hay clientes asignados a este segmento']);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM Segmentos WHERE segmento_id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Segmento eliminado correctamente']);
    } elseif ($action == 'get_one') {
        $id = $_POST['id'];
        $stmt = $conn->prepare("SELECT * FROM Segmentos WHERE segmento_id = ?");
        $stmt->execute([$id]);
        $segmento = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($segmento);
    } else {
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
