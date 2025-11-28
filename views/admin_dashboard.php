<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'empleado') {
    header("Location: login.php");
    exit();
}

// Include data logic
require_once '../php/dashboard_data.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Atlantic City</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9;
            overflow-x: hidden;
        }
        
        /* Sidebar */
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background: linear-gradient(180deg, #1a1a1a 0%, #0f0f0f 100%);
            padding-top: 20px;
            color: #fff;
            transition: all 0.3s;
            z-index: 1000;
        }
        
        .sidebar-header {
            text-align: center;
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }
        
        .sidebar-header h3 {
            color: #d4af37;
            font-weight: 700;
            font-size: 1.5rem;
            margin: 0;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-menu li {
            padding: 0;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 15px 25px;
            color: #aaa;
            text-decoration: none;
            transition: all 0.3s;
            font-weight: 500;
            border-left: 4px solid transparent;
        }
        
        .sidebar-menu a:hover, .sidebar-menu a.active {
            color: #fff;
            background-color: rgba(212, 175, 55, 0.1);
            border-left-color: #d4af37;
        }
        
        .sidebar-menu i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        
        /* Navbar */
        .top-navbar {
            background-color: #fff;
            padding: 15px 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            background-color: #d4af37;
            color: #000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        /* KPI Cards */
        .kpi-card {
            background: #fff;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            transition: transform 0.3s;
            border-bottom: 4px solid transparent;
            height: 100%;
        }
        
        .kpi-card:hover {
            transform: translateY(-5px);
        }
        
        .kpi-card.blue { border-bottom-color: #4e73df; }
        .kpi-card.green { border-bottom-color: #1cc88a; }
        .kpi-card.orange { border-bottom-color: #f6c23e; }
        .kpi-card.red { border-bottom-color: #e74a3b; }
        
        .kpi-icon {
            font-size: 2rem;
            margin-bottom: 15px;
            opacity: 0.8;
        }
        
        .kpi-title {
            color: #888;
            font-size: 0.9rem;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .kpi-value {
            color: #333;
            font-size: 2rem;
            font-weight: 700;
        }
        
        .text-blue { color: #4e73df; }
        .text-green { color: #1cc88a; }
        .text-orange { color: #f6c23e; }
        .text-red { color: #e74a3b; }

    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>Atlantic City</h3>
        </div>
        <ul class="sidebar-menu">
            <li><a href="admin_dashboard.php" class="active"><i class="fas fa-chart-line"></i> Dashboard</a></li>
            <li><a href="clientes.php"><i class="fas fa-users"></i> Gestión de Clientes</a></li>
            <li><a href="#"><i class="fas fa-ticket-alt"></i> Promociones</a></li>
            <li><a href="#"><i class="fas fa-exclamation-triangle"></i> Incidencias</a></li>
            <li><a href="#"><i class="fas fa-gamepad"></i> Análisis de Juegos</a></li>
            <li style="margin-top: 50px;"><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div>
                <h4 class="m-0 text-dark">Dashboard General</h4>
                <small class="text-muted"><?php setlocale(LC_TIME, 'es_ES.UTF-8'); echo date("d \d\e F \d\e Y"); ?></small>
            </div>
            <div class="user-info">
                <div class="text-end">
                    <div class="fw-bold">Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?></div>
                    <small class="text-muted">Administrador</small>
                </div>
                <div class="user-avatar">
                    <?php echo strtoupper(substr($_SESSION['nombre'], 0, 1)); ?>
                </div>
            </div>
        </div>

        <!-- KPI Cards Row -->
        <div class="row g-4">
            <!-- Total Clientes -->
            <div class="col-md-3">
                <div class="kpi-card blue">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="kpi-title">Total Clientes</div>
                            <div class="kpi-value"><?php echo number_format($total_clientes); ?></div>
                        </div>
                        <i class="fas fa-users kpi-icon text-blue"></i>
                    </div>
                </div>
            </div>

            <!-- Visitas Hoy -->
            <div class="col-md-3">
                <div class="kpi-card green">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="kpi-title">Visitas Hoy</div>
                            <div class="kpi-value"><?php echo number_format($visitas_hoy); ?></div>
                        </div>
                        <i class="fas fa-walking kpi-icon text-green"></i>
                    </div>
                </div>
            </div>

            <!-- Incidencias Pendientes -->
            <div class="col-md-3">
                <div class="kpi-card red">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="kpi-title">Incidencias Pendientes</div>
                            <div class="kpi-value"><?php echo number_format($incidencias_pendientes); ?></div>
                        </div>
                        <i class="fas fa-exclamation-circle kpi-icon text-red"></i>
                    </div>
                </div>
            </div>

            <!-- Promociones Activas -->
            <div class="col-md-3">
                <div class="kpi-card orange">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="kpi-title">Promociones Activas</div>
                            <div class="kpi-value"><?php echo number_format($promociones_activas); ?></div>
                        </div>
                        <i class="fas fa-tags kpi-icon text-orange"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div style="height: 400px;">
                            <canvas id="visitasChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Pass PHP data to JS -->
    <script>
        const chartData = <?php echo $chart_json; ?>;
    </script>
    
    <script src="../assets/js/dashboard_charts.js"></script>
</body>
</html>
