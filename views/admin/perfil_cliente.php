<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'empleado') {
    header("Location: ../public/login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: clientes.php");
    exit();
}

$cliente_id = $_GET['id'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Cliente - Atlantic City</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
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
        .profile-header { background: white; padding: 20px; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .nav-tabs .nav-link { color: #495057; }
        .nav-tabs .nav-link.active { color: #d4af37; font-weight: 600; border-bottom: 3px solid #d4af37; }
        .stat-card { background: white; padding: 15px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); text-align: center; }
        .stat-value { font-size: 1.5rem; font-weight: 700; color: #d4af37; }
        .stat-label { font-size: 0.9rem; color: #6c757d; }
    </style>
</head>
<body>

    <?php include '../../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-dark">Perfil de Cliente</h2>
                <a href="clientes.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i> Volver</a>
            </div>

            <!-- Profile Header -->
            <div class="profile-header d-flex align-items-center gap-4">
                <div class="bg-light rounded-circle d-flex justify-content-center align-items-center" style="width: 80px; height: 80px;">
                    <i class="fas fa-user fa-3x text-secondary"></i>
                </div>
                <div class="flex-grow-1">
                    <h3 class="mb-1" id="clienteNombre">Cargando...</h3>
                    <p class="text-muted mb-0"><i class="fas fa-id-card me-2"></i>DNI: <span id="clienteDni">-</span></p>
                    <p class="text-muted mb-0"><i class="fas fa-envelope me-2"></i><span id="clienteEmail">-</span></p>
                </div>
                <div class="text-end">
                    <span class="badge bg-warning text-dark fs-6 mb-2" id="clienteSegmento">-</span>
                    <br>
                    <small class="text-muted">Registrado: <span id="clienteFecha">-</span></small>
                </div>
            </div>

            <!-- Stats Row -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-value" id="totalVisitas">0</div>
                        <div class="stat-label">Visitas Totales</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-value" id="totalJuegos">0</div>
                        <div class="stat-label">Sesiones de Juego</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-value" id="totalPromociones">0</div>
                        <div class="stat-label">Promociones Canjeadas</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-value" id="totalIncidencias">0</div>
                        <div class="stat-label">Incidencias</div>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom-0">
                    <ul class="nav nav-tabs card-header-tabs" id="profileTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="visitas-tab" data-bs-toggle="tab" href="#visitas" role="tab">Historial de Visitas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="juegos-tab" data-bs-toggle="tab" href="#juegos" role="tab">Actividad de Juego</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="promociones-tab" data-bs-toggle="tab" href="#promociones" role="tab">Promociones</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="incidencias-tab" data-bs-toggle="tab" href="#incidencias" role="tab">Incidencias</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="info-tab" data-bs-toggle="tab" href="#info" role="tab">Información Personal</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="profileTabsContent">
                        
                        <!-- Visitas -->
                        <div class="tab-pane fade show active" id="visitas" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover" id="tablaVisitas">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Detalles</th>
                                            <th>Preferencias</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Juegos -->
                        <div class="tab-pane fade" id="juegos" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover" id="tablaJuegos">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Juego</th>
                                            <th>Tipo</th>
                                            <th>Resultado</th>
                                            <th>Puntaje</th>
                                            <th>Duración (min)</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Promociones -->
                        <div class="tab-pane fade" id="promociones" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover" id="tablaPromociones">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Promoción</th>
                                            <th>Descripción</th>
                                            <th>Fecha Asignación</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Incidencias -->
                        <div class="tab-pane fade" id="incidencias" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover" id="tablaIncidencias">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Tipo</th>
                                            <th>Descripción</th>
                                            <th>Estado</th>
                                            <th>Solución</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Info Personal -->
                        <div class="tab-pane fade" id="info" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Dirección:</strong> <span id="infoDireccion">-</span></p>
                                    <p><strong>Teléfono:</strong> <span id="infoTelefono">-</span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Preferencias:</strong> <span id="infoPreferencias">-</span></p>
                                    <p><strong>Fecha Nacimiento:</strong> <span id="infoNacimiento">-</span></p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const clienteId = <?php echo $cliente_id; ?>;

        $(document).ready(function() {
            loadProfile();
        });

        function loadProfile() {
            const formData = new FormData();
            formData.append('action', 'get_profile');
            formData.append('id', clienteId);

            fetch('../../php/cliente_controller.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const c = data.cliente;
                    
                    // Header Info
                    $('#clienteNombre').text(c.nombre);
                    $('#clienteDni').text(c.dni);
                    $('#clienteEmail').text(c.correo);
                    $('#clienteSegmento').text(c.segmento_nombre || 'Sin Asignar');
                    $('#clienteFecha').text(c.fecha_registro);

                    // Stats
                    $('#totalVisitas').text(data.visitas.length);
                    $('#totalJuegos').text(data.juegos.length);
                    $('#totalPromociones').text(data.promociones.filter(p => p.estado === 'Canjeada').length);
                    $('#totalIncidencias').text(data.incidencias.length);

                    // Tab: Info
                    $('#infoDireccion').text(c.direccion);
                    $('#infoTelefono').text(c.telefono);
                    $('#infoPreferencias').text(c.preferencias);
                    $('#infoNacimiento').text(c.fecha_nacimiento);

                    // Tab: Visitas
                    const visitasBody = $('#tablaVisitas tbody');
                    visitasBody.empty();
                    data.visitas.forEach(v => {
                        visitasBody.append(`
                            <tr>
                                <td>${v.fecha_hora}</td>
                                <td>${v.detalles}</td>
                                <td>${v.preferencias_visita || '-'}</td>
                            </tr>
                        `);
                    });

                    // Tab: Juegos
                    const juegosBody = $('#tablaJuegos tbody');
                    juegosBody.empty();
                    data.juegos.forEach(j => {
                        const duracion = Math.round(j.duracion_segundos / 60);
                        const resultadoClass = j.resultado === 'Ganó' ? 'text-success' : 'text-danger';
                        juegosBody.append(`
                            <tr>
                                <td>${j.fecha_jugada}</td>
                                <td>${j.juego}</td>
                                <td>${j.tipo}</td>
                                <td class="${resultadoClass} fw-bold">${j.resultado}</td>
                                <td>${j.puntaje_obtenido}</td>
                                <td>${duracion} min</td>
                            </tr>
                        `);
                    });

                    // Tab: Promociones
                    const promoBody = $('#tablaPromociones tbody');
                    promoBody.empty();
                    data.promociones.forEach(p => {
                        let badgeClass = 'bg-secondary';
                        if(p.estado === 'Canjeada') badgeClass = 'bg-success';
                        if(p.estado === 'Enviada') badgeClass = 'bg-primary';
                        
                        promoBody.append(`
                            <tr>
                                <td>${p.nombre}</td>
                                <td>${p.descripcion}</td>
                                <td>${p.fecha_asignacion}</td>
                                <td><span class="badge ${badgeClass}">${p.estado}</span></td>
                            </tr>
                        `);
                    });

                    // Tab: Incidencias
                    const inciBody = $('#tablaIncidencias tbody');
                    inciBody.empty();
                    data.incidencias.forEach(i => {
                        let badgeClass = 'bg-warning text-dark';
                        if(i.estado === 'Resuelto') badgeClass = 'bg-success';
                        
                        inciBody.append(`
                            <tr>
                                <td>${i.fecha_registro}</td>
                                <td>${i.tipo}</td>
                                <td>${i.descripcion}</td>
                                <td><span class="badge ${badgeClass}">${i.estado}</span></td>
                                <td>${i.solucion || '-'}</td>
                            </tr>
                        `);
                    });

                } else {
                    alert('Error al cargar perfil: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>
