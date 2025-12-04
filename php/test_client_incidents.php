<?php
// Simulate Session
session_start();
$_SESSION['usuario_id'] = 1; // Client ID 1
$_SESSION['tipo_usuario'] = 'cliente';

// Test 1: Create Incident
$_POST['action'] = 'create_incident';
$_POST['tipo_incidencia_id'] = 2; // Sugerencia
$_POST['descripcion'] = 'Test incident from script';
include 'client_portal_controller.php';

echo "\n---\n";

// Test 2: Get Dashboard Data (Check if incident appears)
$_POST['action'] = 'get_dashboard_data';
include 'client_portal_controller.php';
?>
