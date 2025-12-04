<?php
require_once '../config/conexion.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

try {
    $conn = Conexion::conectar();

    if ($action == 'create' || $action == 'update') {
        $nombre = $_POST['nombre'];
        $dni = $_POST['dni'];
        $fecha_nacimiento = $_POST['fecha_nacimiento'];
        $direccion = $_POST['direccion'];
        $correo = $_POST['correo'];
        $telefono = $_POST['telefono'];
        $preferencias = $_POST['preferencias'];
        $segmento_id = $_POST['segmento_id']; // Changed from segmento string
        // Default password for new users
        $password = password_hash('123456', PASSWORD_DEFAULT); 

        if ($action == 'create') {
            // Validate DNI uniqueness
            $check = $conn->prepare("SELECT cliente_id FROM clientes WHERE dni = ?");
            $check->execute([$dni]);
            if ($check->rowCount() > 0) {
                echo json_encode(['success' => false, 'message' => 'El DNI ya está registrado']);
                exit;
            }

            $sql = "INSERT INTO clientes (nombre, dni, fecha_nacimiento, direccion, correo, telefono, preferencias, segmento_id, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$nombre, $dni, $fecha_nacimiento, $direccion, $correo, $telefono, $preferencias, $segmento_id, $password]);
            echo json_encode(['success' => true, 'message' => 'Cliente creado correctamente']);
        } else {
            $id = $_POST['cliente_id'];

            // Validate DNI uniqueness (excluding self)
            $check = $conn->prepare("SELECT cliente_id FROM clientes WHERE dni = ? AND cliente_id != ?");
            $check->execute([$dni, $id]);
            if ($check->rowCount() > 0) {
                echo json_encode(['success' => false, 'message' => 'El DNI ya está registrado por otro cliente']);
                exit;
            }

            // Validate Email uniqueness (excluding self)
            $checkEmail = $conn->prepare("SELECT cliente_id FROM clientes WHERE correo = ? AND cliente_id != ?");
            $checkEmail->execute([$correo, $id]);
            if ($checkEmail->rowCount() > 0) {
                echo json_encode(['success' => false, 'message' => 'El correo ya está registrado por otro cliente']);
                exit;
            }

            $sql = "UPDATE clientes SET nombre=?, dni=?, fecha_nacimiento=?, direccion=?, correo=?, telefono=?, preferencias=?, segmento_id=? WHERE cliente_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$nombre, $dni, $fecha_nacimiento, $direccion, $correo, $telefono, $preferencias, $segmento_id, $id]);
            echo json_encode(['success' => true, 'message' => 'Cliente actualizado correctamente']);
        }
    } elseif ($action == 'delete') {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM clientes WHERE cliente_id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Cliente eliminado correctamente']);
    } elseif ($action == 'get_one') {
        $id = $_POST['id'];
        $stmt = $conn->prepare("SELECT * FROM clientes WHERE cliente_id = ?");
        $stmt->execute([$id]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($cliente);
    } elseif ($action == 'get_profile') {
        $id = $_POST['id'];
        
        // 1. Client Info
        $stmt = $conn->prepare("SELECT c.*, s.nombre as segmento_nombre FROM clientes c LEFT JOIN segmentos s ON c.segmento_id = s.segmento_id WHERE c.cliente_id = ?");
        $stmt->execute([$id]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cliente) {
            echo json_encode(['success' => false, 'message' => 'Cliente no encontrado']);
            exit;
        }

        // 2. Visits
        $stmt = $conn->prepare("SELECT * FROM visitas WHERE cliente_id = ? ORDER BY fecha_hora DESC");
        $stmt->execute([$id]);
        $visitas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. Promotions
        $stmt = $conn->prepare("SELECT cp.*, p.nombre, p.descripcion FROM cliente_promociones cp JOIN promociones p ON cp.promocion_id = p.promocion_id WHERE cp.cliente_id = ? ORDER BY cp.fecha_asignacion DESC");
        $stmt->execute([$id]);
        $promociones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 4. Incidents
        $stmt = $conn->prepare("SELECT i.*, t.nombre as tipo FROM incidencias i JOIN tipo_incidencia t ON i.tipo_incidencia_id = t.tipo_incidencia_id WHERE i.cliente_id = ? ORDER BY i.fecha_registro DESC");
        $stmt->execute([$id]);
        $incidencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 5. Gaming Sessions
        $stmt = $conn->prepare("SELECT sj.*, j.nombre as juego, j.tipo FROM sesiones_juego sj JOIN juegos j ON sj.juego_id = j.juego_id WHERE sj.cliente_id = ? ORDER BY sj.fecha_jugada DESC");
        $stmt->execute([$id]);
        $juegos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'cliente' => $cliente,
            'visitas' => $visitas,
            'promociones' => $promociones,
            'incidencias' => $incidencias,
            'juegos' => $juegos
        ]);
    } elseif ($action == 'list') {
        // Join with Segmentos to get segment name
        $sql = "SELECT c.*, s.nombre as segmento_nombre FROM clientes c LEFT JOIN segmentos s ON c.segmento_id = s.segmento_id";
        $stmt = $conn->query($sql);
        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['data' => $clientes]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
    }

} catch (Exception $e) {
    // Handle duplicate entry error specifically
    if ($e->getCode() == 23000) {
        echo json_encode(['success' => false, 'message' => 'Error: Datos duplicados (DNI o Correo ya registrados)']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
?>
