<?php
require_once 'config/conexion.php';

try {
    $conn = Conexion::conectar();
    
    // Add area_id
    $sql = "ALTER TABLE incidencias ADD COLUMN area_id INT AFTER empleado_id";
    try { $conn->exec($sql); echo "Added area_id\n"; } catch (Exception $e) { echo "area_id might exist\n"; }
    
    // Add FK for area_id
    $sql = "ALTER TABLE incidencias ADD CONSTRAINT fk_incidencia_area FOREIGN KEY (area_id) REFERENCES areas(area_id) ON DELETE SET NULL";
    try { $conn->exec($sql); echo "Added FK for area_id\n"; } catch (Exception $e) { echo "FK might exist\n"; }

    // Add prioridad
    $sql = "ALTER TABLE incidencias ADD COLUMN prioridad VARCHAR(20) DEFAULT 'Media' AFTER tipo_incidencia_id";
    try { $conn->exec($sql); echo "Added prioridad\n"; } catch (Exception $e) { echo "prioridad might exist\n"; }

    // Add fecha_resolucion
    $sql = "ALTER TABLE incidencias ADD COLUMN fecha_resolucion DATETIME AFTER fecha_registro";
    try { $conn->exec($sql); echo "Added fecha_resolucion\n"; } catch (Exception $e) { echo "fecha_resolucion might exist\n"; }

    // Add formulario (file path)
    $sql = "ALTER TABLE incidencias ADD COLUMN formulario VARCHAR(255) AFTER descripcion";
    try { $conn->exec($sql); echo "Added formulario\n"; } catch (Exception $e) { echo "formulario might exist\n"; }

    echo "Schema update completed successfully.";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
