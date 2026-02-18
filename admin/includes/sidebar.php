<?php
// Determinar página activa
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <i class="ph ph-lightning"></i> <span>HYPE<span class="neon">.</span></span>
    </div>
    <ul class="nav-menu">
        <li>
            <a href="dashboard.php" class="nav-item <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                <i class="ph ph-squares-four"></i> <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="products.php" class="nav-item <?php echo ($current_page == 'products.php' || $current_page == 'product_form.php') ? 'active' : ''; ?>">
                <i class="ph ph-package"></i> <span>Productos</span>
            </a>
        </li>
        <li>
            <a href="orders.php" class="nav-item <?php echo ($current_page == 'orders.php' || $current_page == 'order_detail.php') ? 'active' : ''; ?>">
                <i class="ph ph-shopping-cart"></i> <span>Pedidos</span>
            </a>
        </li>
        <li>
            <a href="settings.php" class="nav-item <?php echo ($current_page == 'settings.php') ? 'active' : ''; ?>">
                <i class="ph ph-gear"></i> <span>Configuración</span>
            </a>
        </li>
        <li>
            <a href="logs.php" class="nav-item <?php echo ($current_page == 'logs.php') ? 'active' : ''; ?>">
                <i class="ph ph-list-dashes"></i> <span>Logs</span>
            </a>
        </li>
        <li>
            <a href="backups.php" class="nav-item <?php echo ($current_page == 'backups.php') ? 'active' : ''; ?>">
                <i class="ph ph-database"></i> <span>Base de Datos</span>
            </a>
        </li>
    </ul>
    <div class="sidebar-footer">
        <a href="logout.php" class="nav-item" style="color: #ff6b6b;">
            <i class="ph ph-sign-out"></i> <span>Cerrar Sesión</span>
        </a>
    </div>
</aside>
