<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'cliente') {
    header("Location: public/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Espacio - Atlantic City</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #121212; color: #fff; }
        .navbar { background-color: #000; border-bottom: 1px solid #333; }
        .gold-text { color: #d4af37; }
        .btn-gold { background: linear-gradient(45deg, #d4af37, #f2d06b); color: #000; font-weight: 600; border: none; }
        .btn-gold:hover { background: linear-gradient(45deg, #f2d06b, #d4af37); }
        .card-dark { background-color: #1e1e1e; border: 1px solid #333; border-radius: 15px; }
        .stat-value { font-size: 2.5rem; font-weight: 700; color: #d4af37; }
        .table-dark { background-color: #1e1e1e; color: #ffffff; }
        .table-dark th { color: #d4af37; border-bottom: 1px solid #555; font-weight: 600; }
        .table-dark td { border-bottom: 1px solid #444; color: #e0e0e0; }
        .table-hover tbody tr:hover { color: #fff; background-color: rgba(255,255,255,0.05); }
        .promo-card { background: linear-gradient(135deg, #2a2a2a 0%, #1a1a1a 100%); border: 1px solid #d4af37; transition: transform 0.3s; }
        .promo-card:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(212, 175, 55, 0.2); }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand gold-text fw-bold" href="#">Atlantic City</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item me-3">
                        <span class="text-light">Hola, <span class="gold-text fw-bold"><?php echo htmlspecialchars($_SESSION['nombre']); ?></span></span>
                    </li>
                    <li class="nav-item">
                        <a href="../auth/logout.php" class="btn btn-outline-warning btn-sm">Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <!-- Welcome & Stats -->
        <div class="row mb-5">
            <div class="col-md-8">
                <h1 class="display-5 fw-bold mb-3">Bienvenido a tu <span class="gold-text">Espacio Exclusivo</span></h1>
                <p class="lead text-light">Aquí puedes ver tus beneficios, historial y promociones personalizadas.</p>
            </div>
            <div class="col-md-4">
                <div class="card card-dark p-4 text-center">
                    <h5 class="text-light mb-2">Tus Puntos Acumulados</h5>
                    <div class="stat-value" id="puntosTotales">0</div>
                    <span class="badge bg-warning text-dark mt-2 fs-6" id="segmentoCliente">Cargando...</span>
                </div>
            </div>
        </div>

        <!-- Promotions -->
        <h3 class="mb-4 border-start border-4 border-warning ps-3">Tus Promociones Activas</h3>
        <div class="row mb-5" id="promocionesContainer">
            <div class="col-12 text-center text-light">Cargando promociones...</div>
        </div>

        <div class="row">
            <!-- Recent Games -->
            <div class="col-lg-6 mb-4">
                <div class="card card-dark h-100">
                    <div class="card-header bg-transparent border-bottom border-secondary">
                        <h5 class="mb-0 text-white"><i class="fas fa-gamepad gold-text me-2"></i> Últimas Jugadas</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover mb-0 align-middle">
                                <thead>
                                    <tr>
                                        <th>Juego</th>
                                        <th>Resultado</th>
                                        <th>Puntaje</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaJuegos"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Visits -->
            <div class="col-lg-6 mb-4">
                <div class="card card-dark h-100">
                    <div class="card-header bg-transparent border-bottom border-secondary">
                        <h5 class="mb-0 text-white"><i class="fas fa-calendar-check gold-text me-2"></i> Historial de Visitas</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover mb-0 align-middle">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Detalles</th>
                                        <th>Preferencias</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaVisitas"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Incidents Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card card-dark">
                    <div class="card-header bg-transparent border-bottom border-secondary d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-white"><i class="fas fa-exclamation-circle gold-text me-2"></i> Mis Incidencias</h5>
                        <button class="btn btn-gold btn-sm" data-bs-toggle="modal" data-bs-target="#incidentModal">
                            <i class="fas fa-plus me-1"></i> Reportar Incidencia
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover mb-0 align-middle">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Tipo</th>
                                        <th>Descripción</th>
                                        <th>Estado</th>
                                        <th>Solución</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaIncidencias"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Incident Modal -->
    <div class="modal fade" id="incidentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-white">Reportar Incidencia</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="incidentForm" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="create_incident">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-light">Tipo de Incidencia</label>
                                <select class="form-select bg-dark text-white border-secondary" id="tipo_incidencia_id" name="tipo_incidencia_id" required>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-light">Área (Opcional)</label>
                                <select class="form-select bg-dark text-white border-secondary" id="area_id" name="area_id">
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-light">Descripción</label>
                            <textarea class="form-control bg-dark text-white border-secondary" name="descripcion" rows="4" required placeholder="Describe tu problema o sugerencia..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-light">Adjuntar Archivo / Evidencia (Opcional)</label>
                            <input class="form-control bg-dark text-white border-secondary" type="file" name="formulario">
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-gold" onclick="submitIncident()">Enviar Reporte</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            loadDashboard();
            loadIncidentTypes();
            loadAreas();
        });

        function loadIncidentTypes() {
            const formData = new FormData();
            formData.append('action', 'get_incident_types');
            fetch('../php/client_portal_controller.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                if(data.success) {
                    const select = $('#tipo_incidencia_id');
                    data.data.forEach(t => {
                        select.append(`<option value="${t.tipo_incidencia_id}">${t.nombre}</option>`);
                    });
                }
            });
        }

        function loadAreas() {
            const formData = new FormData();
            formData.append('action', 'get_areas');
            fetch('../php/client_portal_controller.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                if(data.success) {
                    const select = $('#area_id');
                    data.data.forEach(a => {
                        select.append(`<option value="${a.area_id}">${a.nombre}</option>`);
                    });
                }
            });
        }

        function submitIncident() {
            const formData = new FormData(document.getElementById('incidentForm'));
            fetch('../php/client_portal_controller.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                if(data.success) {
                    alert('Incidencia reportada con éxito');
                    $('#incidentModal').modal('hide');
                    document.getElementById('incidentForm').reset();
                    loadDashboard(); // Reload to see new incident
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }

        function loadDashboard() {
            const formData = new FormData();
            formData.append('action', 'get_dashboard_data');

            fetch('../php/client_portal_controller.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Stats
                    $('#puntosTotales').text(data.cliente.puntos_totales);
                    $('#segmentoCliente').text(data.cliente.segmento_nombre || 'Sin Segmento');

                    // Promotions
                    const promoContainer = $('#promocionesContainer');
                    promoContainer.empty();
                    if (data.promociones.length > 0) {
                        data.promociones.forEach(p => {
                            promoContainer.append(`
                                <div class="col-md-4 mb-3">
                                    <div class="card promo-card h-100 text-white p-3">
                                        <div class="card-body">
                                            <h5 class="card-title gold-text">${p.nombre}</h5>
                                            <p class="card-text small text-light">${p.descripcion}</p>
                                            <div class="d-flex justify-content-between align-items-center mt-3">
                                                <small class="text-white-50">Vence: ${p.fecha_fin}</small>
                                                <span class="badge bg-gold text-dark">Activa</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `);
                        });
                    } else {
                        promoContainer.html('<div class="col-12 text-light">No tienes promociones activas en este momento.</div>');
                    }

                    // Games
                    const juegosBody = $('#tablaJuegos');
                    juegosBody.empty();
                    data.juegos.forEach(j => {
                        const resultColor = j.resultado === 'Ganó' ? 'text-success' : 'text-danger';
                        juegosBody.append(`
                            <tr>
                                <td>${j.juego}</td>
                                <td class="${resultColor} fw-bold">${j.resultado}</td>
                                <td>+${j.puntaje_obtenido}</td>
                                <td>${j.fecha_jugada.split(' ')[0]}</td>
                            </tr>
                        `);
                    });

                    // Visits
                    const visitasBody = $('#tablaVisitas');
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

                    // Incidents
                    const incBody = $('#tablaIncidencias');
                    incBody.empty();
                    if (data.incidencias && data.incidencias.length > 0) {
                        data.incidencias.forEach(i => {
                            let badge = 'bg-secondary';
                            if(i.estado === 'Pendiente') badge = 'bg-danger';
                            if(i.estado === 'En Proceso') badge = 'bg-warning text-dark';
                            if(i.estado === 'Resuelto') badge = 'bg-success';
                            
                            incBody.append(`
                                <tr>
                                    <td>${i.fecha_registro}</td>
                                    <td>${i.tipo}</td>
                                    <td>${i.descripcion}</td>
                                    <td><span class="badge ${badge}">${i.estado}</span></td>
                                    <td>${i.solucion || '-'}</td>
                                </tr>
                            `);
                        });
                    } else {
                        incBody.html('<tr><td colspan="5" class="text-center text-muted">No has reportado incidencias.</td></tr>');
                    }

                } else {
                    console.error('Error loading dashboard:', data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>
