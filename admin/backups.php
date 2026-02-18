<?php
/**
 * Admin Backups - HYPE Sportswear CMS
 * Gestión de copias de seguridad
 */

session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../includes/db.php';
$admin_username = $_SESSION['admin_username'] ?? 'Administrador';

// Leer archivos de backup
$backupDir = __DIR__ . '/backups/';
$files = [];
if (file_exists($backupDir)) {
    $scanned = scandir($backupDir);
    foreach ($scanned as $file) {
        if ($file !== '.' && $file !== '..' && $file !== '.htaccess' && pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
            $files[] = [
                'name' => $file,
                'size' => round(filesize($backupDir . $file) / 1024, 2) . ' KB',
                'date' => date('d/m/Y H:i:s', filemtime($backupDir . $file))
            ];
        }
    }
}
// Ordenar por fecha desc
usort($files, function($a, $b) {
    return strtotime(str_replace('/', '-', $b['date'])) - strtotime(str_replace('/', '-', $a['date']));
});
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backups - HYPE CMS</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="css/dashboard.css">
    
    <style>
        .backup-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            background: var(--bg-card);
            padding: 1.5rem;
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }

        .btn-create {
            background: var(--primary-color);
            color: #000;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        .btn-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(214, 254, 0, 0.2);
        }

        .backups-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        .backups-table th {
            text-align: left;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-muted);
        }
        .backups-table td {
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .btn-download {
            color: #4ade80;
            text-decoration: none;
            background: rgba(34, 197, 94, 0.1);
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .btn-download:hover { background: rgba(34, 197, 94, 0.2); }
    </style>
</head>
<body>
    
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-wrapper" id="main-wrapper">
        <?php 
        $page_title = 'Copias de Seguridad';
        include 'includes/topbar.php'; 
        ?>

        <main class="dashboard-content">

            <?php if (isset($_GET['success'])): ?>
                <div style="background: rgba(34, 197, 94, 0.1); color: #4ade80; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    ✅ Backup generado correctamente.
                </div>
            <?php endif; ?>

            <div class="backup-header">
                <div>
                    <h2 style="margin-bottom: 5px;">Generar nuevo respaldo</h2>
                    <p style="color: var(--text-muted); font-size: 0.9rem;">Guarda una copia completa de productos, pedidos y configuraciones.</p>
                </div>
                <a href="actions/backup_create.php" class="btn-create">
                    <i class="ph ph-download-simple"></i> CREAR BACKUP AHORA
                </a>
            </div>

            <div class="table-container">
                <table class="backups-table">
                    <thead>
                        <tr>
                            <th>Archivo</th>
                            <th>Fecha</th>
                            <th>Tamaño</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($files)): ?>
                            <tr><td colspan="4" style="text-align:center; padding:2rem;">No hay backups disponibles.</td></tr>
                        <?php else: ?>
                            <?php foreach($files as $f): ?>
                            <tr>
                                <td style="font-family: monospace; color: #ccc;"><i class="ph ph-file-sql"></i> <?php echo $f['name']; ?></td>
                                <td><?php echo $f['date']; ?></td>
                                <td><?php echo $f['size']; ?></td>
                                <td>
                                    <a href="actions/backup_download.php?file=<?php echo $f['name']; ?>" class="btn-download">
                                        <i class="ph ph-download"></i> Descargar
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </main>
    </div>
    <script src="js/admin.js"></script>
</body>
</html>
