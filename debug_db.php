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

    // Inspect Clientes
    if (in_array('Clientes', $tables) || in_array('clientes', $tables)) {
        echo "<h2>Estructura de 'Clientes':</h2>";
        $stmt = $conn->query("DESCRIBE Clientes");
        echo "<pre>";
        print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
        echo "</pre>";

        echo "<h2>Datos en 'Clientes' (Primeros 5):</h2>";
        $stmt = $conn->query("SELECT * FROM Clientes LIMIT 5");
        echo "<pre>";
        print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
        echo "</pre>";
    } else {
        echo "<h2 style='color:red'>La tabla 'Clientes' NO existe.</h2>";
    }

    // Inspect Empleados
    if (in_array('Empleados', $tables) || in_array('empleados', $tables)) {
        echo "<h2>Estructura de 'Empleados':</h2>";
        $stmt = $conn->query("DESCRIBE Empleados");
        echo "<pre>";
        print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
        echo "</pre>";

        echo "<h2>Datos en 'Empleados' (Primeros 5):</h2>";
        $stmt = $conn->query("SELECT * FROM Empleados LIMIT 5");
        echo "<pre>";
        print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
        echo "</pre>";
    } else {
        echo "<h2 style='color:red'>La tabla 'Empleados' NO existe.</h2>";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
