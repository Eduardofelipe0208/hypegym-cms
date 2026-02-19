<?php
/**
 * Admin Order Detail - HYPE Sportswear CMS
 */

session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../includes/db.php';

$orderId = $_GET['id'] ?? 0;
$order = dbQueryOne("
    SELECT * FROM orders WHERE id = ?", 
    [$orderId]
);

if (!$order) {
    die("Pedido no encontrado.");
}

$items = dbQuery("
    SELECT oi.*, p.name as current_name, p.image_url 
    FROM order_items oi 
    LEFT JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?", 
    [$orderId]
);

$admin_username = $_SESSION['admin_username'] ?? 'Administrador';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle Pedido #<?php echo $order['id']; ?> - HYPE CMS</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="css/dashboard.css">
    
    <style>
        .detail-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .detail-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .info-group h3 {
            font-size: 1rem;
            color: var(--text-muted);
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.8rem;
            font-size: 0.95rem;
        }
        
        .info-row label { color: #888; }
        .info-row span { font-weight: 500; color: white; }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .items-table th {
            text-align: left;
            padding: 1rem;
            background: rgba(255,255,255,0.05);
            color: var(--text-muted);
        }

        .items-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        .item-thumb {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            background: #000;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-muted);
            text-decoration: none;
            margin-bottom: 1rem;
            font-weight: 500;
        }
        .back-btn:hover { color: var(--primary-color); }
    </style>
</head>
<body>
    
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-wrapper">
        <?php 
        $page_title = 'Detalle del Pedido';
        include 'includes/topbar.php'; 
        ?>

        <main class="dashboard-content">
            
            <a href="orders.php" class="back-btn"><i class="ph ph-arrow-left"></i> Volver al listado</a>

            <div class="detail-card">
                <div class="detail-header">
                    <div>
                        <h1 style="font-size:2rem; font-family:'Chakra Petch';">Pedido #<?php echo $order['id']; ?></h1>
                        <span style="color:var(--text-muted);"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></span>
                    </div>
                    <div>
                         <?php 
                            $statuses = [
                                'pending' => ['Pendiente', '#facc15'],
                                'completed' => ['Aprobado', '#4ade80'],
                                'cancelled' => ['Cancelado', '#f87171'],
                            ];
                            $st = $statuses[$order['status']] ?? [$order['status'], '#fff'];
                         ?>
                        <span style="
                            padding: 8px 16px; 
                            border-radius: 4px; 
                            background: <?php echo $st[1]; ?>20; 
                            color: <?php echo $st[1]; ?>; 
                            border: 1px solid <?php echo $st[1]; ?>40;
                            font-weight: 700;
                            text-transform: uppercase;">
                            <?php echo $st[0]; ?>
                        </span>
                    </div>
                </div>

                <div class="info-grid">
                    <!-- Cliente -->
                    <div class="info-group">
                        <h3>Información del Cliente</h3>
                        <div class="info-row"><label>Nombre:</label> <span><?php echo htmlspecialchars($order['customer_name']); ?></span></div>
                        <div class="info-row"><label>Teléfono:</label> <span><?php echo htmlspecialchars($order['customer_phone']); ?></span></div>
                        <div class="info-row"><label>Email:</label> <span><?php echo htmlspecialchars($order['customer_email']); ?></span></div>
                        <div class="info-row"><label>Dirección:</label> <span><?php echo nl2br(htmlspecialchars($order['customer_address'])); ?></span></div>
                    </div>

                    <!-- Pago -->
                    <div class="info-group">
                        <h3>Detalles de Pago</h3>
                        <div class="info-row"><label>Método:</label> <span><?php echo htmlspecialchars($order['payment_method']); ?></span></div>
                        <div class="info-row"><label>Referencia:</label> <span><?php echo htmlspecialchars($order['payment_reference']); ?></span></div>
                        <div class="info-row"><label>Tasa de Cambio:</label> <span>Bs. <?php echo number_format($order['exchange_rate'], 2); ?></span></div>
                        <div class="info-row total" style="margin-top:1rem; font-size:1.2rem;">
                            <label style="color:white;">Total USD:</label> 
                            <span style="color:var(--primary-color);">$<?php echo number_format($order['total_amount'], 2); ?></span>
                        </div>
                        <div class="info-row">
                            <label>Total Bs:</label> 
                            <span>Bs. <?php echo number_format($order['total_amount'] * $order['exchange_rate'], 2); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="detail-card">
                <h3>Productos (<?php echo count($items); ?>)</h3>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Producto</th>
                            <th>Talla</th>
                            <th>Precio Unit.</th>
                            <th>Cantidad</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($items as $item): ?>
                        <tr>
                            <td>
                                <?php if(isset($item['image_url']) && $item['image_url']): ?>
                                <img src="../<?php echo $item['image_url']; ?>" class="item-thumb">
                                <?php else: ?>
                                <div class="item-thumb" style="background:#222;"></div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($item['product_name'] ?? $item['current_name'] ?? 'Producto no disponible (ID: '.$item['product_id'].')'); ?></td>
                            <td><?php echo htmlspecialchars($item['size']); ?></td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td>x<?php echo $item['quantity']; ?></td>
                            <td style="color:var(--primary-color); font-weight:600;">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </main>
    </div>
</body>
</html>
