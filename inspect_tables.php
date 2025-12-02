<?php
require_once 'config/conexion.php';

try {
    $conn = Conexion::conectar();
    $tables = ['visitas', 'incidencias', 'promociones'];

    foreach ($tables as $table) {
        echo "Table: $table\n";
        $stmt = $conn->query("DESCRIBE $table");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $col) {
            echo "  " . $col['Field'] . " (" . $col['Type'] . ")\n";
        }
        echo "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
