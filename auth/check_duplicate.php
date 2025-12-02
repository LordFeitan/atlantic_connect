<?php
require_once '../config/conexion.php';

header('Content-Type: application/json');

$field = $_POST['field'] ?? '';
$value = $_POST['value'] ?? '';

if (!$field || !$value) {
    echo json_encode(['exists' => false]);
    exit;
}

try {
    $conn = Conexion::conectar();
    
    // Whitelist allowed fields to prevent SQL injection via column name
    $allowed_fields = ['dni', 'correo'];
    if (!in_array($field, $allowed_fields)) {
        echo json_encode(['exists' => false]);
        exit;
    }

    $exclude_id = $_POST['exclude_id'] ?? null;

    $sql = "SELECT cliente_id FROM clientes WHERE $field = :val";
    $params = [':val' => $value];

    if ($exclude_id) {
        $sql .= " AND cliente_id != :exclude_id";
        $params[':exclude_id'] = $exclude_id;
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['exists' => true]);
    } else {
        echo json_encode(['exists' => false]);
    }

} catch (Exception $e) {
    echo json_encode(['exists' => false, 'error' => $e->getMessage()]);
}
?>
