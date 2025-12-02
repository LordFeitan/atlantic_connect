<?php
// Configuración de Despliegue

// 1. Credenciales FTP
define('FTP_HOST', 'ftp.tu-sitio.com');
define('FTP_USER', 'tu_usuario_ftp');
define('FTP_PASS', 'tu_contraseña_ftp');
define('FTP_ROOT', '/public_html/'); // Carpeta raíz en el servidor (ej. /public_html/ o /)

// 2. Credenciales Base de Datos REMOTA (Producción)
define('DB_HOST', 'localhost'); // Generalmente es localhost en servidores compartidos
define('DB_NAME', 'nombre_base_datos_prod');
define('DB_USER', 'usuario_db_prod');
define('DB_PASS', 'password_db_prod');

// 3. URL del Sitio (para ejecutar la migración automáticamente)
define('SITE_URL', 'http://tu-sitio.com');

// 4. Archivos/Carpetas a ignorar en la subida
$ignored_files = [
    '.git',
    '.vscode',
    'deployment',
    'tests',
    'README.md',
    'requerimientos.txt',
    'contexto.txt',
    'database_creation.sql',
    'deploy.php',
    'config.php'
];
?>
