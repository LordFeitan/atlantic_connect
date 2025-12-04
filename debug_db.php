<?php
require_once 'config/conexion.php';

try {
    $conn = Conexion::conectar();
    echo "<h1>Diagnóstico de Base de Datos</h1>";

    // List Tables
    echo "<h2>Tablas en la Base de Datos:</h2>";
    $stmt = $conn->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>$table</li>";
    }
    echo "</ul>";

    // Inspect clientes
    if (in_array('clientes', $tables) || in_array('Clientes', $tables)) {
        echo "<h2>Estructura de 'clientes':</h2>";
        $stmt = $conn->query("DESCRIBE clientes");
        echo "<pre>";
        print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
        echo "</pre>";

        echo "<h2>Datos en 'clientes' (Primeros 5):</h2>";
        $stmt = $conn->query("SELECT * FROM clientes LIMIT 5");
        echo "<pre>";
        print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
        echo "</pre>";
    } else {
        echo "<h2 style='color:red'>La tabla 'clientes' NO existe.</h2>";
    }

    // Inspect empleados
    if (in_array('empleados', $tables) || in_array('Empleados', $tables)) {
        echo "<h2>Estructura de 'empleados':</h2>";
        $stmt = $conn->query("DESCRIBE empleados");
        echo "<pre>";
        print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
        echo "</pre>";

        echo "<h2>Datos en 'empleados' (Primeros 5):</h2>";
        $stmt = $conn->query("SELECT * FROM empleados LIMIT 5");
        echo "<pre>";
        print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
        echo "</pre>";
    } else {
        echo "<h2 style='color:red'>La tabla 'empleados' NO existe.</h2>";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
