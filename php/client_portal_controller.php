<?php
require_once '../config/conexion.php';

header('Content-Type: application/json');

session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'cliente') {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$action = $_POST['action'] ?? '';
$cliente_id = $_SESSION['usuario_id'];

try {
    $conn = Conexion::conectar();

    if ($action == 'get_dashboard_data') {
        // 1. Client Info (Points, Segment)
        // Note: Points are calculated from gaming sessions for now
        $stmt = $conn->prepare("
            SELECT c.*, s.nombre as segmento_nombre,
            (SELECT COALESCE(SUM(puntaje_obtenido), 0) FROM sesiones_juego WHERE cliente_id = c.cliente_id) as puntos_totales
            FROM clientes c 
            LEFT JOIN segmentos s ON c.segmento_id = s.segmento_id 
            WHERE c.cliente_id = ?
        ");
        $stmt->execute([$cliente_id]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Active Promotions
        $stmt = $conn->prepare("
            SELECT p.*, cp.estado as estado_asignacion 
            FROM promociones p 
            JOIN cliente_promociones cp ON p.promocion_id = cp.promocion_id 
            WHERE cp.cliente_id = ? AND p.fecha_fin >= CURDATE()
            ORDER BY p.fecha_fin ASC
        ");
        $stmt->execute([$cliente_id]);
        $promociones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. Recent Games (Last 5)
        $stmt = $conn->prepare("
            SELECT sj.*, j.nombre as juego 
            FROM sesiones_juego sj 
            JOIN juegos j ON sj.juego_id = j.juego_id 
            WHERE sj.cliente_id = ? 
            ORDER BY sj.fecha_jugada DESC 
            LIMIT 5
        ");
        $stmt->execute([$cliente_id]);
        $juegos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 4. Recent Visits (Last 5)
        $stmt = $conn->prepare("SELECT * FROM visitas WHERE cliente_id = ? ORDER BY fecha_hora DESC LIMIT 5");
        $stmt->execute([$cliente_id]);
        $visitas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 5. My Incidents
        $stmt = $conn->prepare("
            SELECT i.*, t.nombre as tipo 
            FROM incidencias i 
            JOIN tipo_incidencia t ON i.tipo_incidencia_id = t.tipo_incidencia_id 
            WHERE i.cliente_id = ? 
            ORDER BY i.fecha_registro DESC
        ");
        $stmt->execute([$cliente_id]);
        $incidencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'cliente' => $cliente,
            'promociones' => $promociones,
            'juegos' => $juegos,
            'visitas' => $visitas,
            'incidencias' => $incidencias
        ]);
    } elseif ($action == 'get_incident_types') {
        $stmt = $conn->query("SELECT * FROM tipo_incidencia ORDER BY nombre");
        $tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $tipos]);
    } elseif ($action == 'get_areas') {
        $stmt = $conn->query("SELECT * FROM areas ORDER BY nombre");
        $areas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $areas]);
    } elseif ($action == 'create_incident') {
        $tipo_id = $_POST['tipo_incidencia_id'];
        $area_id = !empty($_POST['area_id']) ? $_POST['area_id'] : null;
        $descripcion = $_POST['descripcion'];
        $prioridad = 'Media'; // Default priority for client reports
        
        // Handle File Upload
        $formulario_path = null;
        if (isset($_FILES['formulario']) && $_FILES['formulario']['error'] == 0) {
            $upload_dir = '../uploads/incidencias/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_ext = pathinfo($_FILES['formulario']['name'], PATHINFO_EXTENSION);
            $file_name = 'inc_' . time() . '_' . $cliente_id . '.' . $file_ext;
            $target_file = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['formulario']['tmp_name'], $target_file)) {
                $formulario_path = 'uploads/incidencias/' . $file_name;
            }
        }

        $sql = "INSERT INTO incidencias (cliente_id, tipo_incidencia_id, area_id, descripcion, formulario, estado, prioridad) VALUES (?, ?, ?, ?, ?, 'Pendiente', ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$cliente_id, $tipo_id, $area_id, $descripcion, $formulario_path, $prioridad]);
        
        echo json_encode(['success' => true, 'message' => 'Incidencia reportada correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
