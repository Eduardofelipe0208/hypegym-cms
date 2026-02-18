<?php
/**
 * Admin Product Form - HYPE Sportswear CMS
 * Formulario único para Crear y Editar productos
 */

session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../db.php';

$product = null;
$title = "Nuevo Producto";
$action = "create";

// Si hay ID, es edición
if (isset($_GET['id'])) {
    $product = dbQueryOne("SELECT * FROM products WHERE id = ?", [$_GET['id']]);
    if ($product) {
        $title = "Editar Producto";
        $action = "update";
    }
}

// Obtener categorías para el select
$categories = dbQuery("SELECT * FROM categories ORDER BY name ASC");
$admin_username = $_SESSION['admin_username'] ?? 'Administrador';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - HYPE CMS</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="css/dashboard.css">
    
    <style>
        .form-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 2rem;
            max-width: 800px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-muted);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            background: #0F1011;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: #fff;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(214, 254, 0, 0.1);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .btn-submit {
            background: var(--primary-color);
            color: #000;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-family: 'Chakra Petch', sans-serif;
            cursor: pointer;
            width: 100%;
            font-size: 1rem;
            transition: transform 0.2s;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
        }

        .btn-cancel {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: var(--text-muted);
            text-decoration: none;
        }
        .btn-cancel:hover { color: #fff; }

        /* Custom Checkbox */
        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }
        .checkbox-wrapper input {
            width: 20px;
            height: 20px;
            accent-color: var(--primary-color);
        }

        /* Image Preview */
        .img-preview {
            width: 100%;
            max-width: 200px;
            margin-top: 10px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            display: none;
        }
        .img-preview.active { display: block; }
    </style>
</head>
<body>
    
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-wrapper" id="main-wrapper">
        <?php 
        $page_title = $title;
        include 'includes/topbar.php'; 
        ?>

        <main class="dashboard-content">

            <div class="form-card">
                <form action="actions/product_actions.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="<?php echo $action; ?>">
                    <?php if ($product): ?>
                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                        <input type="hidden" name="current_image" value="<?php echo $product['image_url']; ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label class="form-label">Nombre del Producto</label>
                        <input type="text" name="name" class="form-control" required placeholder="Ej: Camiseta Oversize Negra" 
                               value="<?php echo $product ? htmlspecialchars($product['name']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Descripción</label>
                        <textarea name="description" class="form-control" placeholder="Detalles del producto..."><?php echo $product ? htmlspecialchars($product['description']) : ''; ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Precio ($)</label>
                            <input type="number" step="0.01" name="price" class="form-control" required placeholder="0.00"
                                   value="<?php echo $product ? $product['price'] : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Stock (Cantidad)</label>
                            <input type="number" name="stock" class="form-control" required placeholder="0"
                                   value="<?php echo $product ? $product['stock'] : '0'; ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tallas (Separadas por coma)</label>
                        <input type="text" name="sizes" class="form-control" placeholder="Ej: S,M,L,XL"
                               value="<?php echo $product ? htmlspecialchars($product['sizes']) : 'S,M,L'; ?>">
                        <small style="color: var(--text-muted); display: block; margin-top: 5px;">Formatos: S,M,L o 38,40,42</small>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Categoría</label>
                            <select name="category_id" class="form-control" required>
                                <option value="">Seleccionar...</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" 
                                        <?php echo ($product && $product['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group" style="display:flex; align-items:center;">
                            <label class="checkbox-wrapper">
                                <input type="checkbox" name="is_featured" value="1" 
                                    <?php echo ($product && $product['is_featured']) ? 'checked' : ''; ?>>
                                <span style="color:#fff;">Producto Destacado (Mostrar en Home)</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Imagen del Producto</label>
                        <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImage(this)">
                        
                        <?php if ($product && $product['image_url']): ?>
                            <img src="../<?php echo $product['image_url']; ?>" class="img-preview active" id="preview">
                        <?php else: ?>
                            <img src="" class="img-preview" id="preview">
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="ph ph-floppy-disk"></i> Guardar Producto
                    </button>
                    
                    <a href="products.php" class="btn-cancel">Cancelar</a>
                </form>
            </div>

        </main>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.add('active');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    <script src="js/admin.js"></script>
</body>
</html>
