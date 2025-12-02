<?php
require_once 'config/conexion.php';

try {
    $conn = Conexion::conectar();
    
    echo "--- Clientes ---\n";
    $stmt = $conn->query("SELECT cliente_id, nombre, password FROM clientes");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: " . $row['cliente_id'] . " | Pass: " . $row['password'] . "\n";
    }

    echo "\n--- Empleados ---\n";
    $stmt = $conn->query("SELECT empleado_id, nombre, password FROM empleados");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: " . $row['empleado_id'] . " | Pass: " . $row['password'] . "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
