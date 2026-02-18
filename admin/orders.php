<?php
/**
 * Admin Orders Management - HYPE Sportswear CMS
 * Panel avanzado con filtros y AJAX
 */

session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../db.php';
$admin_username = $_SESSION['admin_username'] ?? 'Administrador';

// Obtener métodos de pago para filtro
$paymentMethods = dbQuery("SELECT id, name FROM payment_methods ORDER BY name ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pedidos - HYPE CMS</title>
    
    <!-- Librerías -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="css/dashboard.css">
    
    <style>
        .filters-container {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            align-items: end;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }

        .filter-group label {
            display: block;
            margin-bottom: 0.8rem;
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 500;
        }

        .filter-input, .filter-select {
            width: 100%;
            padding: 12px 16px;
            background: #0F1011;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: #fff;
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .filter-input:focus, .filter-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(214, 254, 0, 0.1);
            outline: none;
        }

        /* Botones */
        .btn-filter, .btn-export {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-family: 'Chakra Petch', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            height: 45px; /* Altura fija para alinear con inputs */
        }

        .btn-filter {
            background: var(--primary-color);
            color: #000;
            border: none;
        }
        .btn-filter:hover {
            background: #c4ec00;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(214, 254, 0, 0.2);
        }

        .btn-export {
            background: rgba(34, 197, 94, 0.1);
            color: #4ade80;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
        .btn-export:hover {
            background: rgba(34, 197, 94, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(74, 222, 128, 0.1);
        }

        /* Tabla Estilizada */
        .table-container {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 0; /* Padding quitado para que la tabla toque los bordes */
            overflow: hidden; /* Para bordes redondeados */
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            text-align: left;
            padding: 1.2rem;
            background: rgba(255, 255, 255, 0.03);
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border-color);
        }

        .data-table td {
            padding: 1.2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: #fff;
            vertical-align: middle;
        }

        .data-table tr:last-child td { border-bottom: none; }
        
        .data-table tr:hover {
            background: rgba(255, 255, 255, 0.02);
        }

        /* Badges */
        .status-badge {
            padding: 6px 12px;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .status-badge::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }
        
        .status-pending { background: rgba(234, 179, 8, 0.15); color: #facc15; border: 1px solid rgba(234, 179, 8, 0.2); }
        .status-paid { background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.2); }
        .status-rejected { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.2); }
        .status-shipped { background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.2); }

        .loader {
            text-align: center;
            padding: 3rem;
            color: var(--text-muted);
        }
    </style>
</head>
<body>
    
    <!-- Sidebar (Reutilizado) -->
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-wrapper" id="main-wrapper">
        <?php 
        $page_title = 'Gestión de Pedidos';
        include 'includes/topbar.php'; 
        ?>

        <main class="dashboard-content">
            
            <!-- Filtros Avanzados -->
            <form id="filterForm" class="filters-container">
                <div class="filter-group">
                    <label>Buscar (Cliente/Ref)</label>
                    <input type="text" name="search" class="filter-input" placeholder="Nombre, ID, Referencia...">
                </div>
                
                <div class="filter-group">
                    <label>Estado</label>
                    <select name="status" class="filter-select">
                        <option value="">Todos</option>
                        <option value="pending">Pendiente</option>
                        <option value="paid">Pagado</option>
                        <option value="shipped">Enviado</option>
                        <option value="rejected">Rechazado</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Método de Pago</label>
                    <select name="payment_method" class="filter-select">
                        <option value="">Todos</option>
                        <?php foreach($paymentMethods as $pm): ?>
                            <option value="<?php echo $pm['id']; ?>"><?php echo htmlspecialchars($pm['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Fecha Desde</label>
                    <input type="date" name="date_start" class="filter-input">
                </div>

                <!-- Botones Acción -->
                <div class="filter-group" style="display: flex; gap: 10px;">
                    <button type="submit" class="btn-filter" style="flex:1;">
                        <i class="ph ph-funnel"></i> Filtrar
                    </button>
                    <a href="#" id="btnExport" class="btn-export" title="Exportar a Excel" target="_blank">
                        <i class="ph ph-file-csv"></i> Exportar
                    </a>
                </div>
            </form>

            <!-- Tabla de Resultados -->
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Total (USD)</th>
                            <th>Total (Bs)</th>
                            <th>Método</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTableBody">
                        <!-- Carga vía AJAX -->
                    </tbody>
                </table>
                <div id="loader" class="loader" style="display:none;">
                    <i class="ph ph-spinner ph-spin" style="font-size: 2rem;"></i>
                    <p>Cargando pedidos...</p>
                </div>
            </div>

        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            loadOrders();

            // Interceptar submit del filtro
            document.getElementById('filterForm').addEventListener('submit', (e) => {
                e.preventDefault();
                loadOrders();
            });
        });

        async function loadOrders() {
            const form = document.getElementById('filterForm');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            
            // Actualizar enlace de exportación
            document.getElementById('btnExport').href = `actions/export_orders.php?${params.toString()}`;

            // Mostrar Loader
            const tbody = document.getElementById('ordersTableBody');
            const loader = document.getElementById('loader');
            tbody.innerHTML = ''; 
            loader.style.display = 'block';

            try {
                const response = await fetch(`../api/orders.php?${params.toString()}`);
                const result = await response.json();
                
                loader.style.display = 'none';

                if (result.error) {
                    tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; color:#f87171;">Error: ${result.error}</td></tr>`;
                    return;
                }

                if (result.data.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding:2rem;">No se encontraron pedidos con estos filtros.</td></tr>`;
                    return;
                }

                result.data.forEach(order => {
                    const row = `
                        <tr>
                            <td style="font-family:'Chakra Petch';">#${order.id}</td>
                            <td>${new Date(order.created_at).toLocaleDateString()}</td>
                            <td>
                                <div style="font-weight:600;">${order.customer_name}</div>
                                <div style="font-size:0.8rem; color:#888;">${order.customer_phone}</div>
                            </td>
                            <td style="font-weight:600;">$${parseFloat(order.total_amount).toFixed(2)}</td>
                            <td style="color:var(--text-muted);">Bs. ${parseFloat(order.total_bs).toLocaleString('es-VE', {minimumFractionDigits: 2})}</td>
                            <td>
                                <div>${order.payment_method_id || 'N/A'}</div>
                                <div style="font-size:0.75rem; color:#888;">Ref: ${order.payment_reference || '-'}</div>
                            </td>
                            <td>
                                <span class="status-badge status-${order.status.toLowerCase()}">${order.status_label}</span>
                            </td>
                            <td>
                                <a href="order_detail.php?id=${order.id}" class="action-btn btn-edit" title="Ver Detalles">
                                    <i class="ph ph-eye"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });

            } catch (error) {
                console.error(error);
                loader.style.display = 'none';
                tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; color:#f87171;">Error de conexión</td></tr>`;
            }
        }
    </script>
    <script src="js/admin.js"></script>
</body>
</html>
