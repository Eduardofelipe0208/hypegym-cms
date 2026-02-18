<?php
/**
 * Admin Dashboard - HYPE Sportswear CMS
 * Panel de control principal
 */

session_start();

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$admin_username = $_SESSION['admin_username'] ?? 'Administrador';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - HYPE CMS</title>
    
    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Iconos Phosphor -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Estilos Dashboard -->
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content Wrapper -->
    <div class="main-wrapper" id="main-wrapper">
        
        <!-- Topbar -->
        <?php 
        $page_title = 'Resumen General';
        include 'includes/topbar.php'; 
        ?>

        <!-- Contenido Principal -->
        <main class="dashboard-content">

            <!-- KPI Cards -->
            <div class="kpi-grid">
                <!-- Total Pedidos -->
                <div class="kpi-card">
                    <div class="kpi-info">
                        <h3>Total Pedidos</h3>
                        <div class="kpi-value" id="total-orders">--</div>
                    </div>
                    <div class="kpi-icon">
                        <i class="ph ph-shopping-bag"></i>
                    </div>
                </div>

                <!-- Ingresos USD -->
                <div class="kpi-card">
                    <div class="kpi-info">
                        <h3>Ingresos (USD)</h3>
                        <div class="kpi-value" id="income-usd">--</div>
                    </div>
                    <div class="kpi-icon">
                        <i class="ph ph-currency-dollar"></i>
                    </div>
                </div>

                <!-- Ingresos Bs -->
                <div class="kpi-card">
                    <div class="kpi-info">
                        <h3>Ingresos (Bs)</h3>
                        <div class="kpi-value" id="income-bs">--</div>
                    </div>
                    <div class="kpi-icon">
                        <i class="ph ph-coins"></i>
                    </div>
                </div>

                <!-- Pedidos Pendientes -->
                <div class="kpi-card">
                    <div class="kpi-info">
                        <h3>Por Despachar</h3>
                        <div class="kpi-value" id="pending-orders" style="color:#ff6b6b">--</div>
                    </div>
                    <div class="kpi-icon" style="background:rgba(255, 107, 107, 0.1); color:#ff6b6b;">
                        <i class="ph ph-clock-counter-clockwise"></i>
                    </div>
                </div>
            </div>

            <!-- Gráficas -->
            <div class="charts-grid">
                <!-- Ventas Semanales -->
                <div class="chart-card full-width">
                    <div class="chart-header">
                        <div class="chart-title">Comportamiento de Ventas (Últimos 7 días)</div>
                        <i class="ph ph-chart-line-up" style="color:var(--primary-color)"></i>
                    </div>
                    <canvas id="salesChart"></canvas>
                </div>

                <!-- Ventas por Mes -->
                <div class="chart-card">
                    <div class="chart-header">
                        <div class="chart-title">Desempeño Mensual (2026)</div>
                    </div>
                    <canvas id="monthlyChart"></canvas>
                </div>

                <!-- Top Productos -->
                <div class="chart-card">
                    <div class="chart-header">
                        <div class="chart-title">Productos Más Vendidos</div>
                    </div>
                    <canvas id="productsChart"></canvas>
                </div>
            </div>

            <!-- Sección Inferior: Pedidos Recientes y Acciones -->
            <div class="dashboard-bottom-grid">
                
                <!-- Tabla de Pedidos Recientes -->
                <div class="recent-orders-card">
                    <div class="card-header">
                        <h3>Pedidos Recientes</h3>
                        <a href="orders.php" class="btn-text">Ver todos</a>
                    </div>
                    <div class="table-responsive">
                        <table class="recent-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody id="recent-orders-body">
                                <!-- Cargado vía JS -->
                                <tr><td colspan="5" class="text-center">Cargando...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Acciones Rápidas -->
                <div class="quick-actions-card">
                    <h3>Acciones Rápidas</h3>
                    <div class="actions-grid">
                        <a href="product_form.php" class="action-item">
                            <div class="icon-box"><i class="ph ph-plus"></i></div>
                            <span>Nuevo Producto</span>
                        </a>
                        <a href="orders.php?status=pending" class="action-item">
                            <div class="icon-box"><i class="ph ph-clock-counter-clockwise"></i></div>
                            <span>Ver Pendientes</span>
                        </a>
                        <a href="settings.php" class="action-item">
                            <div class="icon-box"><i class="ph ph-gear"></i></div>
                            <span>Configuración</span>
                        </a>
                        <a href="shop.php" target="_blank" class="action-item">
                            <div class="icon-box"><i class="ph ph-globe"></i></div>
                            <span>Ver Tienda</span>
                        </a>
                    </div>
                </div>

            </div>

        </main>
    </div>

    <!-- Dashboard Logic -->
    <script src="js/admin.js"></script>
    <script src="js/dashboard.js"></script>
</body>
</html>
