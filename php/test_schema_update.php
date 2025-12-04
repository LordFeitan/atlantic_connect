<?php
// Simulate Session
session_start();
$_SESSION['usuario_id'] = 1; 
$_SESSION['tipo_usuario'] = 'cliente';

// Mock File Upload
$_FILES['formulario'] = [
    'name' => 'test_evidence.txt',
    'type' => 'text/plain',
    'tmp_name' => tempnam(sys_get_temp_dir(), 'test'),
    'error' => 0,
    'size' => 123
];
file_put_contents($_FILES['formulario']['tmp_name'], 'This is a test evidence file content.');

$_POST['action'] = 'create_incident';
$_POST['tipo_incidencia_id'] = 1; // Queja
$_POST['area_id'] = 1; // Operaciones
$_POST['descripcion'] = 'Test incident with Area and File';

include 'client_portal_controller.php';

// Clean up temp file
unlink($_FILES['formulario']['tmp_name']);
?>
