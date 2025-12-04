<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'empleado') {
    header("Location: ../public/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Promociones - Atlantic City</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
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
        .card-header-gold { background: linear-gradient(135deg, #d4af37 0%, #b48f26 100%); color: #000; font-weight: 600; }
        .btn-gold { background-color: #d4af37; color: #000; border: none; font-weight: 600; }
        .btn-gold:hover { background-color: #b48f26; color: #000; }
    </style>
</head>
<body>

    <?php include '../../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="container-fluid">
            <h2 class="mb-4 fw-bold text-dark">Gestión de Promociones</h2>
            
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header card-header-gold rounded-top-4 d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-ticket-alt me-2"></i> Listado de Promociones</span>
                    <button class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#promocionModal" onclick="resetForm()">
                        <i class="fas fa-plus"></i> Nueva Promoción
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tablaPromociones" class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Inicio</th>
                                    <th>Fin</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="promocionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalTitle">Nueva Promoción</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="promocionForm">
                        <input type="hidden" id="promocion_id" name="promocion_id">
                        <input type="hidden" id="action" name="action" value="create">
                        
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="2"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-gold" onclick="guardarPromocion()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            cargarPromociones();
        });

        function cargarPromociones() {
            $('#tablaPromociones').DataTable({
                ajax: {
                    url: '../../php/promocion_controller.php',
                    type: 'POST',
                    data: { action: 'list' }
                },
                columns: [
                    { data: 'promocion_id' },
                    { data: 'nombre' },
                    { data: 'descripcion' },
                    { data: 'fecha_inicio' },
                    { data: 'fecha_fin' },
                    {
                        data: null,
                        render: function(data, type, row) {
                            const today = new Date().toISOString().split('T')[0];
                            if (row.fecha_fin < today) {
                                return '<span class="badge bg-secondary">Finalizada</span>';
                            } else if (row.fecha_inicio > today) {
                                return '<span class="badge bg-info">Programada</span>';
                            } else {
                                return '<span class="badge bg-success">Activa</span>';
                            }
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `
                                <button class="btn btn-sm btn-warning" onclick="editarPromocion(${row.promocion_id})"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-danger" onclick="eliminarPromocion(${row.promocion_id})"><i class="fas fa-trash"></i></button>
                            `;
                        }
                    }
                ],
                language: { url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json' },
                destroy: true,
                order: [[3, 'desc']]
            });
        }

        function resetForm() {
            $('#promocionForm')[0].reset();
            $('#promocion_id').val('');
            $('#action').val('create');
            $('#modalTitle').text('Nueva Promoción');
        }

        function guardarPromocion() {
            const formData = new FormData(document.getElementById('promocionForm'));
            
            fetch('../../php/promocion_controller.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Éxito', data.message, 'success');
                    $('#promocionModal').modal('hide');
                    cargarPromociones();
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            });
        }

        function editarPromocion(id) {
            const formData = new FormData();
            formData.append('action', 'get_one');
            formData.append('id', id);

            fetch('../../php/promocion_controller.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                $('#promocion_id').val(data.promocion_id);
                $('#nombre').val(data.nombre);
                $('#descripcion').val(data.descripcion);
                $('#fecha_inicio').val(data.fecha_inicio);
                $('#fecha_fin').val(data.fecha_fin);
                $('#action').val('update');
                $('#modalTitle').text('Editar Promoción');
                new bootstrap.Modal(document.getElementById('promocionModal')).show();
            });
        }

        function eliminarPromocion(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "No podrás revertir esto",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('action', 'delete');
                    formData.append('id', id);

                    fetch('../../php/promocion_controller.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Eliminado', data.message, 'success');
                            cargarPromociones();
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    });
                }
            });
        }
    </script>
</body>
</html>
