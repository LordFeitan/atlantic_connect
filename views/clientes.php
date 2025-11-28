<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'empleado') {
    header("Location: login.php");
    exit();
}
require_once '../config/conexion.php';
$conn = Conexion::conectar();
$stmt = $conn->query("SELECT * FROM Clientes");
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>Atlantic City</h3>
        </div>
        <ul class="sidebar-menu">
            <li><a href="admin_dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
            <li><a href="clientes.php" class="active"><i class="fas fa-users"></i> Gestión de Clientes</a></li>
            <li><a href="#"><i class="fas fa-ticket-alt"></i> Promociones</a></li>
            <li><a href="#"><i class="fas fa-exclamation-triangle"></i> Incidencias</a></li>
            <li><a href="#"><i class="fas fa-gamepad"></i> Análisis de Juegos</a></li>
            <li style="margin-top: 50px;"><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gestión de Clientes</h2>
            <button class="btn btn-gold" data-bs-toggle="modal" data-bs-target="#clienteModal" onclick="resetForm()">
                <i class="fas fa-plus"></i> Nuevo Cliente
            </button>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">
                <table id="tablaClientes" class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
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
                            <td><?php echo htmlspecialchars($c['correo']); ?></td>
                            <td><?php echo htmlspecialchars($c['telefono']); ?></td>
                            <td>
                                <span class="badge <?php echo $c['segmento'] == 'VIP' ? 'bg-warning text-dark' : 'bg-secondary'; ?>">
                                    <?php echo htmlspecialchars($c['segmento']); ?>
                                </span>
                            </td>
                            <td>
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

    <!-- Modal -->
    <div class="modal fade" id="clienteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nuevo Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="clienteForm">
                        <input type="hidden" id="cliente_id" name="cliente_id">
                        <input type="hidden" id="action" name="action" value="create">
                        
                        <div class="mb-3">
                            <label class="form-label">Nombre Completo</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correo" name="correo" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Preferencias</label>
                            <textarea class="form-control" id="preferencias" name="preferencias"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Segmento</label>
                            <select class="form-select" id="segmento" name="segmento">
                                <option value="Regular">Regular</option>
                                <option value="VIP">VIP</option>
                                <option value="Nuevo">Nuevo</option>
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
    <script src="../assets/js/clientes.js"></script>
</body>
</html>
