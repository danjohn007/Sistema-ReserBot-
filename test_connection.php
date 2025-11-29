<?php
/**
 * ReserBot - Archivo de prueba de conexión y URL base
 * 
 * Este archivo verifica:
 * 1. La conexión a la base de datos MySQL
 * 2. La configuración de la URL base
 * 3. Los requisitos del sistema
 */

// Cargar configuración
require_once __DIR__ . '/config/config.php';

// Función para mostrar estado
function showStatus($condition, $successMsg, $errorMsg) {
    if ($condition) {
        echo "<div style='color: green; margin: 5px 0;'>✓ $successMsg</div>";
        return true;
    } else {
        echo "<div style='color: red; margin: 5px 0;'>✗ $errorMsg</div>";
        return false;
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Conexión - ReserBot</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        h1 {
            color: #3B82F6;
            margin-bottom: 20px;
        }
        h2 {
            color: #374151;
            border-bottom: 2px solid #E5E7EB;
            padding-bottom: 10px;
            margin-top: 0;
        }
        .success {
            background: #D1FAE5;
            color: #065F46;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .error {
            background: #FEE2E2;
            color: #991B1B;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid #E5E7EB;
        }
        th {
            background: #F3F4F6;
        }
        .btn {
            display: inline-block;
            background: #3B82F6;
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            margin-top: 20px;
        }
        .btn:hover {
            background: #1E40AF;
        }
        code {
            background: #F3F4F6;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>🔧 ReserBot - Test de Conexión</h1>
        
        <h2>1. Requisitos del Sistema</h2>
        <?php
        $phpOk = showStatus(
            version_compare(PHP_VERSION, '7.4.0', '>='),
            "PHP " . PHP_VERSION . " instalado",
            "Se requiere PHP 7.4 o superior. Versión actual: " . PHP_VERSION
        );
        
        $pdoOk = showStatus(
            extension_loaded('pdo') && extension_loaded('pdo_mysql'),
            "Extensión PDO MySQL disponible",
            "La extensión PDO MySQL no está instalada"
        );
        
        $jsonOk = showStatus(
            extension_loaded('json'),
            "Extensión JSON disponible",
            "La extensión JSON no está instalada"
        );
        
        $mbstringOk = showStatus(
            extension_loaded('mbstring'),
            "Extensión mbstring disponible",
            "La extensión mbstring no está instalada (recomendada)"
        );
        
        $sessionOk = showStatus(
            extension_loaded('session'),
            "Extensión session disponible",
            "La extensión session no está instalada"
        );
        ?>
    </div>
    
    <div class="card">
        <h2>2. Configuración URL Base</h2>
        <table>
            <tr>
                <th>Parámetro</th>
                <th>Valor</th>
            </tr>
            <tr>
                <td>URL Base Detectada</td>
                <td><code><?= htmlspecialchars(BASE_URL) ?></code></td>
            </tr>
            <tr>
                <td>URL Pública</td>
                <td><code><?= htmlspecialchars(PUBLIC_URL) ?></code></td>
            </tr>
            <tr>
                <td>Ruta del Proyecto</td>
                <td><code><?= htmlspecialchars(ROOT_PATH) ?></code></td>
            </tr>
            <tr>
                <td>Zona Horaria</td>
                <td><code><?= htmlspecialchars(APP_TIMEZONE) ?></code></td>
            </tr>
        </table>
        
        <?php
        showStatus(
            !empty(BASE_URL),
            "URL base configurada correctamente",
            "No se pudo detectar la URL base"
        );
        ?>
    </div>
    
    <div class="card">
        <h2>3. Conexión a Base de Datos</h2>
        <table>
            <tr>
                <th>Parámetro</th>
                <th>Valor</th>
            </tr>
            <tr>
                <td>Host</td>
                <td><code><?= htmlspecialchars(DB_HOST) ?></code></td>
            </tr>
            <tr>
                <td>Base de Datos</td>
                <td><code><?= htmlspecialchars(DB_NAME) ?></code></td>
            </tr>
            <tr>
                <td>Usuario</td>
                <td><code><?= htmlspecialchars(DB_USER) ?></code></td>
            </tr>
            <tr>
                <td>Charset</td>
                <td><code><?= htmlspecialchars(DB_CHARSET) ?></code></td>
            </tr>
        </table>
        
        <?php
        $dbConnected = false;
        $dbError = '';
        $dbVersion = '';
        $tablesExist = false;
        
        try {
            $dsn = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            
            // Verificar si la base de datos existe
            $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . DB_NAME . "'");
            $dbExists = $stmt->fetch();
            
            if ($dbExists) {
                $pdo = new PDO(
                    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, 
                    DB_USER, 
                    DB_PASS,
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
                $dbConnected = true;
                
                // Obtener versión de MySQL
                $stmt = $pdo->query("SELECT VERSION()");
                $dbVersion = $stmt->fetchColumn();
                
                // Verificar si las tablas existen
                $stmt = $pdo->query("SHOW TABLES LIKE 'usuarios'");
                $tablesExist = $stmt->fetch() !== false;
            } else {
                $dbError = "La base de datos '" . DB_NAME . "' no existe. Ejecute el archivo sql/schema.sql";
            }
            
        } catch (PDOException $e) {
            $dbError = $e->getMessage();
        }
        
        echo "<div style='margin-top: 15px;'>";
        showStatus($dbConnected, "Conexión a MySQL exitosa (v$dbVersion)", "Error de conexión: $dbError");
        
        if ($dbConnected) {
            showStatus($tablesExist, "Las tablas del sistema existen", "Las tablas no existen. Ejecute sql/schema.sql");
        }
        echo "</div>";
        ?>
    </div>
    
    <div class="card">
        <h2>4. Permisos de Escritura</h2>
        <?php
        $uploadDir = PUBLIC_PATH . '/uploads';
        $uploadWritable = is_writable(PUBLIC_PATH) || (is_dir($uploadDir) && is_writable($uploadDir));
        
        showStatus(
            $uploadWritable,
            "El directorio public tiene permisos de escritura",
            "El directorio public no tiene permisos de escritura para uploads"
        );
        ?>
    </div>
    
    <?php 
    $allOk = $phpOk && $pdoOk && $jsonOk && $sessionOk && $dbConnected && $tablesExist;
    ?>
    
    <div class="card">
        <h2>Resultado Final</h2>
        
        <?php if ($allOk): ?>
        <div class="success">
            <strong>✓ ¡Todo está configurado correctamente!</strong><br>
            El sistema ReserBot está listo para usarse.
        </div>
        
        <a href="<?= BASE_URL ?>/" class="btn">
            Ir al Sistema →
        </a>
        
        <p style="margin-top: 15px; color: #6B7280;">
            <strong>Enlaces directos:</strong><br>
            <a href="<?= BASE_URL ?>/login.php" style="color: #3B82F6;">Iniciar Sesión</a> | 
            <a href="<?= BASE_URL ?>/registro.php" style="color: #3B82F6;">Registrarse</a>
        </p>
        
        <?php else: ?>
        <div class="error">
            <strong>✗ Hay problemas que necesitan resolverse</strong><br>
            Revise los errores marcados arriba antes de continuar.
        </div>
        
        <?php if (!$dbConnected || !$tablesExist): ?>
        <h3 style="margin-top: 20px;">Pasos para configurar la base de datos:</h3>
        <ol>
            <li>Acceda a phpMyAdmin o a la línea de comandos de MySQL</li>
            <li>Ejecute el archivo <code>sql/schema.sql</code></li>
            <li>Recargue esta página</li>
        </ol>
        
        <pre style="background: #1F2937; color: #E5E7EB; padding: 15px; border-radius: 8px; overflow-x: auto; margin-top: 15px;">
mysql -u <?= DB_USER ?> -p < sql/schema.sql</pre>
        <?php endif; ?>
        
        <?php endif; ?>
    </div>
    
    <div style="text-align: center; color: #6B7280; margin-top: 20px; font-size: 0.9em;">
        ReserBot v<?= APP_VERSION ?> - Sistema de Reservaciones y Citas Profesionales<br>
        Prueba de conexión realizada: <?= date('d/m/Y H:i:s') ?>
    </div>
</body>
</html>
