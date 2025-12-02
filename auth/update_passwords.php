<?php
// Adjust path to config
require_once __DIR__ . '/../config/conexion.php';

try {
    $conn = Conexion::conectar();
    echo "Iniciando migración de contraseñas...\n";

    // 1. Migrar Clientes
    $stmt = $conn->query("SELECT cliente_id, password FROM clientes");
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $countClientes = 0;
    foreach ($clientes as $cliente) {
        // Verificar si ya es un hash (los hashes de bcrypt empiezan con $2y$)
        if (strpos($cliente['password'], '$2y$') !== 0) {
            $newHash = password_hash($cliente['password'], PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE clientes SET password = :pass WHERE cliente_id = :id");
            $update->execute([':pass' => $newHash, ':id' => $cliente['cliente_id']]);
            $countClientes++;
            echo "Cliente ID " . $cliente['cliente_id'] . " actualizado.\n";
        }
    }
    echo "Total Clientes actualizados: $countClientes\n";

    // 2. Migrar Empleados
    $stmt = $conn->query("SELECT empleado_id, password FROM empleados");
    $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $countEmpleados = 0;
    foreach ($empleados as $empleado) {
        if (strpos($empleado['password'], '$2y$') !== 0) {
            $newHash = password_hash($empleado['password'], PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE empleados SET password = :pass WHERE empleado_id = :id");
            $update->execute([':pass' => $newHash, ':id' => $empleado['empleado_id']]);
            $countEmpleados++;
            echo "Empleado ID " . $empleado['empleado_id'] . " actualizado.\n";
        }
    }
    echo "Total Empleados actualizados: $countEmpleados\n";

    echo "Migración completada exitosamente.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
