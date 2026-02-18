<?php
/**
 * Admin Logs - HYPE Sportswear CMS
 * Visor de actividades del sistema
 */

session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../db.php';
$admin_username = $_SESSION['admin_username'] ?? 'Administrador';

// Filtros
$userFilter = $_GET['user_id'] ?? '';
$dateFilter = $_GET['date'] ?? '';

// Construir Consulta
$sql = "SELECT l.*, u.username 
        FROM logs l 
        LEFT JOIN users u ON l.user_id = u.id 
        WHERE 1=1";
$params = [];

if (!empty($userFilter)) {
    $sql .= " AND l.user_id = ?";
    $params[] = $userFilter;
}

if (!empty($dateFilter)) {
    $sql .= " AND DATE(l.created_at) = ?";
    $params[] = $dateFilter;
}

$sql .= " ORDER BY l.created_at DESC LIMIT 100";

$logs = dbQuery($sql, $params);

// Obtener lista de usuarios para el filtro
$users = dbQuery("SELECT id, username FROM users ORDER BY username ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs de Sistema - HYPE CMS</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="css/dashboard.css">
    
    <style>
        .filters-bar {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            gap: 1rem;
            align-items: flex-end;
        }

        .filter-item { flex: 1; max-width: 250px; }
        .filter-item label { display: block; margin-bottom: 0.5rem; font-size: 0.85rem; color: var(--text-muted); }
        .filter-input {
            width: 100%;
            padding: 10px;
            background: #0F1011;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: #fff;
            font-family: 'Inter', sans-serif;
        }

        .btn-filter {
            background: var(--primary-color);
            color: #000;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logs-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        .logs-table th {
            text-align: left;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.85rem;
        }
        .logs-table td {
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 0.9rem;
        }
        .logs-table tr:hover td { background: rgba(255, 255, 255, 0.02); }

        .log-action {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            background: rgba(255, 255, 255, 0.1);
            color: #ccc;
        }
        .action-login { color: #4ade80; background: rgba(34, 197, 94, 0.1); }
        .action-logout { color: #facc15; background: rgba(234, 179, 8, 0.1); }
        .action-create { color: #60a5fa; background: rgba(59, 130, 246, 0.1); }
        .action-delete { color: #f87171; background: rgba(239, 68, 68, 0.1); }
        
        .log-meta { font-size: 0.8rem; color: #666; }
    </style>
</head>
<body>
    
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-wrapper" id="main-wrapper">
        <?php 
        $page_title = 'Logs de Actividad';
        include 'includes/topbar.php'; 
        ?>

        <main class="dashboard-content">
            
            <form class="filters-bar" method="GET">
                <div class="filter-item">
                    <label>Usuario</label>
                    <select name="user_id" class="filter-input">
                        <option value="">Todos</option>
                        <?php foreach($users as $u): ?>
                            <option value="<?php echo $u['id']; ?>" <?php if($userFilter == $u['id']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($u['username']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-item">
                    <label>Fecha</label>
                    <input type="date" name="date" class="filter-input" value="<?php echo htmlspecialchars($dateFilter); ?>">
                </div>

                <button type="submit" class="btn-filter">
                    <i class="ph ph-funnel"></i> Filtrar
                </button>
            </form>

            <div class="table-container">
                <table class="logs-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th>Descripción</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                            <tr><td colspan="5" style="text-align:center; padding:2rem;">No hay registros recientes.</td></tr>
                        <?php else: ?>
                            <?php foreach($logs as $log): 
                                $actionClass = '';
                                if (strpos($log['action'], 'login') !== false) $actionClass = 'action-login';
                                elseif (strpos($log['action'], 'logout') !== false) $actionClass = 'action-logout';
                                elseif (strpos($log['action'], 'creado') !== false) $actionClass = 'action-create';
                                elseif (strpos($log['action'], 'eliminado') !== false) $actionClass = 'action-delete';
                            ?>
                            <tr>
                                <td class="log-meta"><?php echo date('d/m/Y H:i:s', strtotime($log['created_at'])); ?></td>
                                <td><?php echo htmlspecialchars($log['username'] ?? 'Sistema'); ?></td>
                                <td><span class="log-action <?php echo $actionClass; ?>"><?php echo htmlspecialchars($log['action']); ?></span></td>
                                <td><?php echo htmlspecialchars($log['description']); ?></td>
                                <td class="log-meta"><?php echo htmlspecialchars($log['ip_address']); ?></td>
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
