<?php
require_once 'config/conexion.php';

try {
    $conn = Conexion::conectar();
    
    // Add DNI column if not exists
    try {
        $conn->exec("ALTER TABLE clientes ADD COLUMN dni VARCHAR(20) UNIQUE AFTER nombre");
        echo "Columna 'dni' agregada.<br>";
    } catch (PDOException $e) {
        echo "Columna 'dni' ya existe o error: " . $e->getMessage() . "<br>";
    }

    // Add fecha_nacimiento column if not exists
    try {
        $conn->exec("ALTER TABLE clientes ADD COLUMN fecha_nacimiento DATE AFTER dni");
        echo "Columna 'fecha_nacimiento' agregada.<br>";
    } catch (PDOException $e) {
        echo "Columna 'fecha_nacimiento' ya existe o error: " . $e->getMessage() . "<br>";
    }

} catch (Exception $e) {
    echo "Error general: " . $e->getMessage();
}
?>
