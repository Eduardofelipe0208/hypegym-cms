<?php
/**
 * Admin Products List - HYPE Sportswear CMS
 * Lista de productos con opciones de gestión
 */

session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../db.php';

// Obtener productos
$products = dbQuery("
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    ORDER BY p.id DESC
");

$admin_username = $_SESSION['admin_username'] ?? 'Administrador';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - HYPE CMS</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="css/dashboard.css">
    
    <style>
        .table-container {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.5rem;
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            color: #fff;
        }

        .data-table th {
            text-align: left;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.03);
            color: var(--text-muted);
            font-weight: 500;
            border-bottom: 1px solid var(--border-color);
            white-space: nowrap;
        }

        .data-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .data-table tr:hover {
            background: rgba(255, 255, 255, 0.02);
        }

        .product-img {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            object-fit: cover;
            background: #2A2B2E;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            color: #fff;
            text-decoration: none;
            transition: all 0.2s;
            margin-right: 4px;
        }

        .btn-edit { background: rgba(59, 130, 246, 0.2); color: #60a5fa; }
        .btn-edit:hover { background: rgba(59, 130, 246, 0.3); }

        .btn-delete { background: rgba(239, 68, 68, 0.2); color: #f87171; }
        .btn-delete:hover { background: rgba(239, 68, 68, 0.3); }

        .btn-add {
            background: var(--primary-color);
            color: #000;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: 'Chakra Petch', sans-serif;
            transition: all 0.3s;
        }
        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(214, 254, 0, 0.2);
        }

        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-stock-high { background: rgba(34, 197, 94, 0.2); color: #4ade80; }
        .badge-stock-low { background: rgba(234, 179, 8, 0.2); color: #facc15; }
        .badge-stock-out { background: rgba(239, 68, 68, 0.2); color: #f87171; }
    </style>
</head>
<body>
    
    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content Wrapper -->
    <div class="main-wrapper" id="main-wrapper">
        
        <!-- Topbar -->
        <?php 
        $page_title = 'Gestión de Productos';
        include 'includes/topbar.php'; 
        ?>

        <main class="dashboard-content">
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h2 style="font-family: 'Chakra Petch'; font-size: 1.5rem;">Listado de Inventario</h2>
                <a href="product_form.php" class="btn-add">
                    <i class="ph ph-plus"></i> Nuevo Producto
                </a>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div style="background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); color: #4ade80; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    ✅ Operación realizada con éxito.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    ❌ Ocurrió un error. <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="80">Imagen</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Estado</th>
                            <th width="100">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p): ?>
                        <tr>
                            <td>
                                <?php 
                                    $img = $p['image_url'] ? '../' . $p['image_url'] : 'https://placehold.co/100x100/1A1B1E/FFF?text=HYPE';
                                ?>
                                <img src="<?php echo htmlspecialchars($img); ?>" alt="Product" class="product-img">
                            </td>
                            <td style="font-weight: 500; font-family: 'Chakra Petch';">
                                <?php echo htmlspecialchars($p['name']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($p['category_name'] ?? 'Sin categoría'); ?></td>
                            <td>$<?php echo number_format($p['price'], 2); ?></td>
                            <td>
                                <?php 
                                    if ($p['stock'] <= 0) echo '<span class="badge badge-stock-out">Agotado</span>';
                                    elseif ($p['stock'] < 10) echo '<span class="badge badge-stock-low">Bajo (' . $p['stock'] . ')</span>';
                                    else echo '<span class="badge badge-stock-high">En stock (' . $p['stock'] . ')</span>';
                                ?>
                            </td>
                            <td>
                                <?php echo $p['is_featured'] ? '<i class="ph ph-star-fill" style="color:#fbbf24" title="Destacado"></i>' : ''; ?>
                            </td>
                            <td>
                                <a href="product_form.php?id=<?php echo $p['id']; ?>" class="action-btn btn-edit" title="Editar">
                                    <i class="ph ph-pencil-simple"></i>
                                </a>
                                <a href="actions/product_actions.php?action=delete&id=<?php echo $p['id']; ?>" 
                                   class="action-btn btn-delete" 
                                   title="Eliminar"
                                   onclick="return confirm('¿Estás seguro de eliminar este producto? Esta acción no se puede deshacer.');">
                                    <i class="ph ph-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 3rem; color: #666;">
                                No hay productos registrados aún.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </main>
    </div>
    <script src="js/admin.js"></script>
</body>
</html>
