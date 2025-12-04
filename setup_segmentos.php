<?php
require_once 'config/conexion.php';

try {
    $conn = Conexion::conectar();
    
    // 1. Create segmentos table
    $sql = "CREATE TABLE IF NOT EXISTS segmentos (
        segmento_id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(50) NOT NULL UNIQUE,
        descripcion TEXT,
        criterios_minimos TEXT
    )";
    $conn->exec($sql);
    echo "Tabla segmentos creada.<br>";

    // 2. Insert default segments if empty
    $defaults = [
        ['Nuevo', 'Clientes recién registrados', 'Registro completo'],
        ['Regular', 'Clientes frecuentes', 'Al menos 1 visita mensual'],
        ['VIP', 'Clientes de alto valor', 'Gasto superior a 5000'],
        ['Platino', 'Clientes exclusivos', 'Invitación directa']
    ];

    foreach ($defaults as $seg) {
        $stmt = $conn->prepare("INSERT IGNORE INTO segmentos (nombre, descripcion, criterios_minimos) VALUES (?, ?, ?)");
        $stmt->execute($seg);
    }
    echo "Datos semilla insertados en segmentos.<br>";

    // 3. Add segmento_id to clientes if not exists
    $check = $conn->query("SHOW COLUMNS FROM clientes LIKE 'segmento_id'");
    if ($check->rowCount() == 0) {
        $conn->exec("ALTER TABLE clientes ADD COLUMN segmento_id INT AFTER segmento");
        $conn->exec("ALTER TABLE clientes ADD FOREIGN KEY (segmento_id) REFERENCES segmentos(segmento_id) ON DELETE SET NULL");
        echo "Columna segmento_id agregada a clientes.<br>";
        
        // 4. Migrate data
        $conn->exec("UPDATE clientes c JOIN segmentos s ON c.segmento = s.nombre SET c.segmento_id = s.segmento_id");
        echo "Datos de clientes migrados a segmento_id.<br>";
        
        // Optional: Drop old column? Better keep it for safety for now, or rename it.
        // $conn->exec("ALTER TABLE clientes DROP COLUMN segmento");
    } else {
        echo "La columna segmento_id ya existe.<br>";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
