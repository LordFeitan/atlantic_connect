<?php
require_once '../config/conexion.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

try {
    $conn = Conexion::conectar();

    if ($action == 'list') {
        $sql = "SELECT i.*, c.nombre as cliente_nombre, t.nombre as tipo_nombre, e.nombre as empleado_nombre 
                FROM incidencias i 
                JOIN clientes c ON i.cliente_id = c.cliente_id 
                JOIN tipo_incidencia t ON i.tipo_incidencia_id = t.tipo_incidencia_id 
                LEFT JOIN empleados e ON i.empleado_id = e.empleado_id 
                ORDER BY i.fecha_registro DESC";
        $stmt = $conn->query($sql);
        $incidencias = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['data' => $incidencias]);
    } elseif ($action == 'create' || $action == 'update') {
        $cliente_id = $_POST['cliente_id'];
        $tipo_id = $_POST['tipo_incidencia_id'];
        $descripcion = $_POST['descripcion'];
        $estado = $_POST['estado'];
        $solucion = $_POST['solucion'] ?? null;
        $empleado_id = !empty($_POST['empleado_id']) ? $_POST['empleado_id'] : null;

        if ($action == 'create') {
            $sql = "INSERT INTO incidencias (cliente_id, tipo_incidencia_id, descripcion, estado, solucion, empleado_id) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$cliente_id, $tipo_id, $descripcion, $estado, $solucion, $empleado_id]);
            echo json_encode(['success' => true, 'message' => 'Incidencia registrada correctamente']);
        } else {
            $id = $_POST['incidencia_id'];
            $sql = "UPDATE incidencias SET cliente_id=?, tipo_incidencia_id=?, descripcion=?, estado=?, solucion=?, empleado_id=? WHERE incidencia_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$cliente_id, $tipo_id, $descripcion, $estado, $solucion, $empleado_id, $id]);
            echo json_encode(['success' => true, 'message' => 'Incidencia actualizada correctamente']);
        }
    } elseif ($action == 'delete') {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM incidencias WHERE incidencia_id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Incidencia eliminada correctamente']);
    } elseif ($action == 'get_one') {
        $id = $_POST['id'];
        $stmt = $conn->prepare("SELECT * FROM incidencias WHERE incidencia_id = ?");
        $stmt->execute([$id]);
        $incidencia = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($incidencia);
    } elseif ($action == 'get_aux_data') {
        // Get Clients
        $stmt = $conn->query("SELECT cliente_id, nombre FROM clientes ORDER BY nombre");
        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get Types
        $stmt = $conn->query("SELECT tipo_incidencia_id, nombre FROM tipo_incidencia ORDER BY nombre");
        $tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get Employees (Optional, for assignment)
        $stmt = $conn->query("SELECT empleado_id, nombre FROM empleados ORDER BY nombre");
        $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'clientes' => $clientes,
            'tipos' => $tipos,
            'empleados' => $empleados
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
