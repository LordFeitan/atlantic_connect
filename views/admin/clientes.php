<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'empleado') {
    header("Location: ../public/login.php");
    exit();
}
require_once '../../config/conexion.php';
$conn = Conexion::conectar();

// Obtener clientes con nombre de segmento
$sql = "SELECT c.*, s.nombre as segmento_nombre FROM clientes c LEFT JOIN segmentos s ON c.segmento_id = s.segmento_id";
$stmt = $conn->query($sql);
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener segmentos para el modal
$stmtSeg = $conn->query("SELECT * FROM segmentos");
$segmentos = $stmtSeg->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes - Atlantic City</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f6f9; }
        .sidebar { height: 100vh; width: 250px; position: fixed; top: 0; left: 0; background: linear-gradient(180deg, #1a1a1a 0%, #0f0f0f 100%); padding-top: 20px; color: #fff; z-index: 1000; }
        .sidebar-header { text-align: center; padding: 20px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); margin-bottom: 20px; }
        .sidebar-header h3 { color: #d4af37; font-weight: 700; font-size: 1.5rem; margin: 0; }
        .sidebar-menu { list-style: none; padding: 0; margin: 0; }
        .sidebar-menu a { display: block; padding: 15px 25px; color: #aaa; text-decoration: none; transition: all 0.3s; font-weight: 500; border-left: 4px solid transparent; }
        .sidebar-menu a:hover, .sidebar-menu a.active { color: #fff; background-color: rgba(212, 175, 55, 0.1); border-left-color: #d4af37; }
        .sidebar-menu i { margin-right: 10px; width: 20px; text-align: center; }
        .main-content { margin-left: 250px; padding: 20px; }
        .btn-gold { background: linear-gradient(135deg, #d4af37 0%, #b48f26 100%); border: none; color: #000; font-weight: 600; }
        .btn-gold:hover { background: linear-gradient(135deg, #f2d06b 0%, #d4af37 100%); }
        .card-header-gold { background: linear-gradient(135deg, #d4af37 0%, #b48f26 100%); color: #000; font-weight: 600; }
    </style>
</head>
<body>

    <?php include '../../includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="mb-4 fw-bold text-dark">Gestión de Clientes</h2>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header card-header-gold rounded-top-4 d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-users me-2"></i> Listado de Clientes</span>
                    <button class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#clienteModal" onclick="resetForm()">
                        <i class="fas fa-plus"></i> Nuevo Cliente
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tablaClientes" class="table table-hover align-middle">
                            <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>DNI</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Segmento</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($clientes as $c): ?>
                        <tr>
                            <td><?php echo $c['cliente_id']; ?></td>
                            <td><?php echo htmlspecialchars($c['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($c['dni'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($c['correo']); ?></td>
                            <td><?php echo htmlspecialchars($c['telefono']); ?></td>
                            <td>
                                <span class="badge bg-primary"><?php echo htmlspecialchars($c['segmento_nombre'] ?? 'Sin Asignar'); ?></span>
                            </td>
                            <td>
                                <a href="perfil_cliente.php?id=<?php echo $c['cliente_id']; ?>" class="btn btn-sm btn-info text-white" title="Ver Perfil"><i class="fas fa-eye"></i></a>
                                <button class="btn btn-sm btn-warning" onclick="editarCliente(<?php echo $c['cliente_id']; ?>)"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-danger" onclick="eliminarCliente(<?php echo $c['cliente_id']; ?>)"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                    </div>
            </div>
        </div>
    </div>
</div>

    <!-- Modal -->
    <div class="modal fade" id="clienteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalTitle">Nuevo Cliente</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="clienteForm">
                        <input type="hidden" id="cliente_id" name="cliente_id">
                        <input type="hidden" id="action" name="action" value="create">
                        
                        <div class="mb-3">
                            <label class="form-label">Nombre Completo</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+" title="Solo letras y espacios">
                            <div class="invalid-feedback">Nombre inválido (solo letras).</div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">DNI</label>
                                <input type="text" class="form-control" id="dni" name="dni" required maxlength="8" pattern="\d{8}">
                                <div class="invalid-feedback" id="dniFeedback">DNI debe tener 8 dígitos.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha Nacimiento</label>
                                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
                                <div class="invalid-feedback">Debe ser mayor de 18 años.</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correo" name="correo" required>
                            <div class="invalid-feedback">Correo inválido.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" maxlength="9" pattern="\d{9}">
                            <div class="invalid-feedback">Teléfono debe tener 9 dígitos.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Preferencias</label>
                            <textarea class="form-control" id="preferencias" name="preferencias"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Segmento</label>
                            <select class="form-select" id="segmento_id" name="segmento_id" required>
                                <?php foreach($segmentos as $seg): ?>
                                    <option value="<?php echo $seg['segmento_id']; ?>"><?php echo htmlspecialchars($seg['nombre']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-gold" onclick="guardarCliente()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../assets/js/clientes.js"></script>
</body>
</html>
