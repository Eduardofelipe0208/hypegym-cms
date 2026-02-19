<?php
/**
 * Instalador Automático - HYPE Sportswear CMS
 */

// Si ya existe db.php, el sistema ya está instalado
if (file_exists('includes/db.php')) {
    header('Location: index.php');
    exit;
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_host = $_POST['db_host'];
    $db_name = $_POST['db_name'];
    $db_user = $_POST['db_user'];
    $db_pass = $_POST['db_pass'];
    
    $admin_user = $_POST['admin_user'];
    $admin_pass = $_POST['admin_pass'];

    try {
        // 1. Conectar sin base de datos para crearla si no existe
        $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
        $pdo = new PDO("mysql:host=$db_host", $db_user, $db_pass, $options);
        
        // 2. Crear Base de Datos
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `$db_name`");
        
        // 3. Ejecutar archivos SQL de maintenance/
        $sqlFiles = glob('maintenance/*.sql');
        sort($sqlFiles); // Ordenar para ejecutar en orden si tienen nombres como 01_..., 02_...

        // Forzar database.sql primero si existe, ya que tiene la estructura base
        $baseSql = 'maintenance/database.sql';
        if (in_array($baseSql, $sqlFiles)) {
            // Mover al principio
            $sqlFiles = array_diff($sqlFiles, [$baseSql]);
            array_unshift($sqlFiles, $baseSql);
        }

        foreach ($sqlFiles as $file) {
            $sql = file_get_contents($file);
            $pdo->exec($sql);
        }

        // 4. Crear/Actualizar Usuario Admin
        $passHash = password_hash($admin_pass, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?) ON DUPLICATE KEY UPDATE password_hash = ?");
        $stmt->execute([$admin_user, $passHash, $passHash]);

        // 5. Generar includes/db.php
        $dbContent = "<?php
/**
 * Configuración de conexión a la base de datos
 * Generado automáticamente por install.php
 */

define('DB_HOST', '$db_host');
define('DB_NAME', '$db_name');
define('DB_USER', '$db_user');
define('DB_PASS', '$db_pass');
define('DB_CHARSET', 'utf8mb4');

// Variable global para la conexión PDO
\$pdo = null;

function getDB() {
    global \$pdo;
    if (\$pdo !== null) return \$pdo;
    
    try {
        \$dsn = \"mysql:host=\" . DB_HOST . \";dbname=\" . DB_NAME . \";charset=\" . DB_CHARSET;
        \$options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => \"SET NAMES \" . DB_CHARSET
        ];
        \$pdo = new PDO(\$dsn, DB_USER, DB_PASS, \$options);
        return \$pdo;
    } catch (PDOException \$e) {
        die(\"Error de conexión: \" . \$e->getMessage());
    }
}

// Funciones helpers
function dbQuery(\$sql, \$params = []) {
    try {
        \$db = getDB();
        \$stmt = \$db->prepare(\$sql);
        \$stmt->execute(\$params);
        return \$stmt->fetchAll();
    } catch (PDOException \$e) {
        error_log(\"Error en query: \" . \$e->getMessage());
        return [];
    }
}

function dbQueryOne(\$sql, \$params = []) {
    try {
        \$db = getDB();
        \$stmt = \$db->prepare(\$sql);
        \$stmt->execute(\$params);
        return \$stmt->fetch();
    } catch (PDOException \$e) {
        error_log(\"Error en query: \" . \$e->getMessage());
        return false;
    }
}

function dbExecute(\$sql, \$params = []) {
    try {
        \$db = getDB();
        \$stmt = \$db->prepare(\$sql);
        \$result = \$stmt->execute(\$params);
        if (stripos(trim(\$sql), 'INSERT') === 0) return \$db->lastInsertId();
        return \$stmt->rowCount();
    } catch (PDOException \$e) {
        error_log(\"Error en execute: \" . \$e->getMessage());
        return false;
    }
}

getDB(); // Inicializar
";
        file_put_contents('includes/db.php', $dbContent);

        // 6. Eliminar instalador (Opcional, por seguridad)
        // unlink(__FILE__); 
        
        // Redirigir
        header('Location: index.php?installed=true');
        exit;

    } catch (PDOException $e) {
        $message = "Error de Base de Datos: " . $e->getMessage();
        $messageType = "error";
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $messageType = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalación - HYPE CMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@500;700&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #D6FE00; --bg: #0F1011; --card: #1A1B1E; --text: #fff; }
        body { background: var(--bg); color: var(--text); font-family: 'Inter', sans-serif; display: grid; place-items: center; min-height: 100vh; margin: 0; }
        .installer-card { background: var(--card); padding: 2rem; border-radius: 12px; width: 100%; max-width: 400px; border: 1px solid #333; }
        h1 { font-family: 'Chakra Petch'; text-align: center; color: var(--primary); margin-top: 0; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; font-size: 0.9rem; color: #aaa; }
        input { width: 100%; padding: 10px; background: #000; border: 1px solid #333; border-radius: 6px; color: #fff; box-sizing: border-box; }
        input:focus { border-color: var(--primary); outline: none; }
        button { width: 100%; padding: 12px; background: var(--primary); color: #000; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-family: 'Chakra Petch'; font-size: 1rem; margin-top: 1rem; }
        button:hover { opacity: 0.9; }
        .alert { padding: 10px; border-radius: 6px; margin-bottom: 1rem; font-size: 0.9rem; }
        .alert.error { background: rgba(255, 0, 0, 0.2); color: #ff6b6b; border: 1px solid #ff6b6b; }
        .section-title { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; color: #666; margin: 1.5rem 0 0.5rem; border-bottom: 1px solid #333; padding-bottom: 5px; }
    </style>
</head>
<body>
    <div class="installer-card">
        <h1>HYPE CMS <span style="font-size:0.5em; vertical-align:middle;">INSTALLER</span></h1>
        
        <?php if ($message): ?>
            <div class="alert <?php echo $messageType; ?>"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="section-title">Base de Datos</div>
            <div class="form-group">
                <label>Servidor (Host)</label>
                <input type="text" name="db_host" value="localhost" required>
            </div>
            <div class="form-group">
                <label>Nombre BD</label>
                <input type="text" name="db_name" value="hype_shop" required>
            </div>
            <div class="form-group">
                <label>Usuario BD</label>
                <input type="text" name="db_user" value="root" required>
            </div>
            <div class="form-group">
                <label>Contraseña BD</label>
                <input type="password" name="db_pass" placeholder="(Dejar vacío si no tiene)">
            </div>

            <div class="section-title">Cuenta Administrador</div>
            <div class="form-group">
                <label>Usuario Admin</label>
                <input type="text" name="admin_user" value="admin" required>
            </div>
            <div class="form-group">
                <label>Contraseña Admin</label>
                <input type="password" name="admin_pass" required>
            </div>

            <button type="submit">INSTALAR SISTEMA</button>
        </form>
    </div>
</body>
</html>
