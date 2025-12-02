<?php
// Script de Despliegue Automático
// Ejecutar desde línea de comandos o navegador: php deploy.php

require_once 'config.php';

// Aumentar tiempo de ejecución
set_time_limit(0);
ini_set('memory_limit', '256M');

echo "<h1>Iniciando Despliegue...</h1>";
echo "<pre>";

// 1. Conexión FTP
$conn_id = ftp_connect(FTP_HOST);
if (!$conn_id) {
    die("❌ Error: No se pudo conectar al servidor FTP " . FTP_HOST);
}

$login_result = ftp_login($conn_id, FTP_USER, FTP_PASS);
if (!$login_result) {
    die("❌ Error: Credenciales FTP incorrectas.");
}

ftp_pasv($conn_id, true); // Modo pasivo
echo "✅ Conectado a FTP correctamente.\n";

// 2. Función Recursiva de Subida
function upload_directory($conn_id, $local_dir, $remote_dir, $ignored) {
    // Crear directorio remoto si no existe
    if (!@ftp_chdir($conn_id, $remote_dir)) {
        if (ftp_mkdir($conn_id, $remote_dir)) {
            echo "📂 Creado directorio: $remote_dir\n";
        } else {
            echo "⚠️ No se pudo crear/entrar al directorio: $remote_dir\n";
        }
    }

    $files = scandir($local_dir);
    foreach ($files as $file) {
        if ($file == '.' || $file == '..') continue;
        if (in_array($file, $ignored)) continue;

        $local_path = $local_dir . '/' . $file;
        $remote_path = $remote_dir . '/' . $file;

        if (is_dir($local_path)) {
            upload_directory($conn_id, $local_path, $remote_path, $ignored);
        } else {
            // Manejo especial para conexion.php
            if ($file == 'conexion.php' && strpos($local_path, 'config') !== false) {
                upload_production_config($conn_id, $remote_path);
            } else {
                if (ftp_put($conn_id, $remote_path, $local_path, FTP_BINARY)) {
                    echo "⬆️ Subido: $file\n";
                } else {
                    echo "❌ Error subiendo: $file\n";
                }
            }
        }
    }
}

// 3. Generar y Subir conexion.php de Producción
function upload_production_config($conn_id, $remote_path) {
    $content = "<?php
class Conexion {
    public static function conectar() {
        \$host = '" . DB_HOST . "';
        \$dbname = '" . DB_NAME . "';
        \$username = '" . DB_USER . "';
        \$password = '" . DB_PASS . "';

        try {
            \$conn = new PDO(\"mysql:host=\$host;dbname=\$dbname;charset=utf8\", \$username, \$password);
            \$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return \$conn;
        } catch (PDOException \$e) {
            die(\"Error de conexión: \" . \$e->getMessage());
        }
    }
}
?>";
    
    // Crear archivo temporal
    $temp_file = tempnam(sys_get_temp_dir(), 'conf');
    file_put_contents($temp_file, $content);
    
    if (ftp_put($conn_id, $remote_path, $temp_file, FTP_BINARY)) {
        echo "⚙️ Configuración de producción subida: $remote_path\n";
    } else {
        echo "❌ Error subiendo configuración de producción.\n";
    }
    unlink($temp_file);
}

// 4. Generar y Subir Script de Migración DB
function upload_migration_script($conn_id, $remote_root) {
    // Leer el SQL local
    $sql_file = '../database_creation.sql';
    if (!file_exists($sql_file)) {
        echo "⚠️ No se encontró database_creation.sql, saltando migración.\n";
        return;
    }
    $sql_content = file_get_contents($sql_file);
    
    // Escapar comillas para PHP string
    $sql_content_escaped = addslashes($sql_content);

    $migration_script = "<?php
    \$host = '" . DB_HOST . "';
    \$dbname = '" . DB_NAME . "';
    \$username = '" . DB_USER . "';
    \$password = '" . DB_PASS . "';

    try {
        \$conn = new PDO(\"mysql:host=\$host;charset=utf8\", \$username, \$password);
        \$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Crear DB si no existe
        \$conn->exec(\"CREATE DATABASE IF NOT EXISTS `\$dbname`\");
        \$conn->exec(\"USE `\$dbname`\");
        
        // Ejecutar SQL
        \$sql = \"$sql_content_escaped\";
        
        // Separar por ; para ejecutar una por una (básico)
        // Nota: Esto es simple y puede fallar con stored procedures complejos
        \$statements = array_filter(array_map('trim', explode(';', \$sql)));

        foreach (\$statements as \$stmt) {
            if (!empty(\$stmt)) {
                try {
                    \$conn->exec(\$stmt);
                } catch (Exception \$e) {
                    // Ignorar errores de 'tabla ya existe' si se desea
                    echo \"Nota: \" . \$e->getMessage() . \"<br>\";
                }
            }
        }
        echo \"✅ Base de datos migrada/actualizada correctamente.\";
        
        // Auto-borrarse por seguridad
        // unlink(__FILE__); 
        echo \"<br>⚠️ Por seguridad, borra este archivo (migrate_temp.php) del servidor.\";

    } catch (PDOException \$e) {
        die(\"Error de migración: \" . \$e->getMessage());
    }
    ?>";

    $temp_file = tempnam(sys_get_temp_dir(), 'mig');
    file_put_contents($temp_file, $migration_script);
    
    $remote_path = $remote_root . '/migrate_temp.php';
    
    if (ftp_put($conn_id, $remote_path, $temp_file, FTP_BINARY)) {
        echo "🚀 Script de migración subido: $remote_path\n";
        
        // Intentar ejecutarlo
        $url = SITE_URL . '/migrate_temp.php';
        echo "🔄 Intentando ejecutar migración en: $url ...\n";
        
        $context = stream_context_create(['http' => ['timeout' => 10]]);
        $response = @file_get_contents($url, false, $context);
        
        if ($response) {
            echo "📥 Respuesta del servidor:\n$response\n";
        } else {
            echo "⚠️ No se pudo ejecutar automáticamente. Por favor visita $url manualmente.\n";
        }
        
    } else {
        echo "❌ Error subiendo script de migración.\n";
    }
    unlink($temp_file);
}

// --- EJECUCIÓN ---

$project_root = realpath(__DIR__ . '/..');
echo "📂 Directorio local: $project_root\n";

// Subir Archivos
upload_directory($conn_id, $project_root, FTP_ROOT, $ignored_files);

// Subir y Ejecutar Migración
upload_migration_script($conn_id, FTP_ROOT);

ftp_close($conn_id);
echo "\n✨ Despliegue Finalizado ✨";
echo "</pre>";
?>
