<?php
require_once '../config/conexion.php';

try {
    $conn = Conexion::conectar();

    // 1. Total Clientes
    $stmt = $conn->query("SELECT COUNT(*) as total FROM clientes");
    $total_clientes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // 2. Visitas Hoy
    $stmt = $conn->query("SELECT COUNT(*) as total FROM visitas WHERE DATE(fecha_hora) = CURDATE()");
    $visitas_hoy = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // 3. Incidencias Pendientes (estado != 'Resuelto')
    $stmt = $conn->query("SELECT COUNT(*) as total FROM incidencias WHERE estado != 'Resuelto'");
    $incidencias_pendientes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // 4. Promociones Activas (fecha_fin >= CURDATE)
    $stmt = $conn->query("SELECT COUNT(*) as total FROM promociones WHERE fecha_fin >= CURDATE()");
    $promociones_activas = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // 5. Chart Data: Visitas por Mes (Last 6 Months)
    // Set Spanish locale for month names if supported by DB config, otherwise handle in PHP or JS.
    // Here we'll fetch Year-Month and handle formatting.
    $stmt = $conn->query("
        SELECT 
            DATE_FORMAT(fecha_hora, '%Y-%m') as mes_anio, 
            COUNT(*) as total 
        FROM visitas 
        WHERE fecha_hora >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY mes_anio 
        ORDER BY mes_anio ASC
    ");
    $chart_data_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $labels = [];
    $data = [];

    // Helper to translate months
    $meses = [
        '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
        '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
        '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
    ];

    foreach ($chart_data_raw as $row) {
        $parts = explode('-', $row['mes_anio']); // [Year, Month]
        $monthNum = $parts[1];
        $labels[] = $meses[$monthNum]; // e.g., "Noviembre"
        $data[] = $row['total'];
    }

    echo json_encode([
        'total_clientes' => $total_clientes,
        'visitas_hoy' => $visitas_hoy,
        'incidencias_pendientes' => $incidencias_pendientes,
        'promociones_activas' => $promociones_activas,
        'chart_labels' => $labels,
        'chart_data' => $data
    ]);

} catch (Exception $e) {
    // Fallback values in case of error
    echo json_encode([
        'total_clientes' => 0,
        'visitas_hoy' => 0,
        'incidencias_pendientes' => 0,
        'promociones_activas' => 0,
        'chart_labels' => [],
        'chart_data' => []
    ]);
}
?>
