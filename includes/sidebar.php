    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>Atlantic City</h3>
        </div>
        <ul class="sidebar-menu">
            <li><a href="admin_dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'active' : ''; ?>"><i class="fas fa-chart-line"></i> Dashboard</a></li>
            <li><a href="clientes.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'clientes.php' ? 'active' : ''; ?>"><i class="fas fa-users"></i> Gestión de Clientes</a></li>
            <li><a href="segmentos.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'segmentos.php' ? 'active' : ''; ?>"><i class="fas fa-layer-group"></i> Segmentos</a></li>
            <li><a href="promociones.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'promociones.php' ? 'active' : ''; ?>"><i class="fas fa-ticket-alt"></i> Promociones</a></li>
            <li><a href="incidencias.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'incidencias.php' ? 'active' : ''; ?>"><i class="fas fa-exclamation-triangle"></i> Incidencias</a></li>
            <li style="margin-top: 50px;"><a href="../../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
        </ul>
    </div>
