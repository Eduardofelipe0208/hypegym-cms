<?php
/**
 * Admin Sections - HYPE Sportswear CMS
 * Gestión de secciones dinámicas (Hero, Banners)
 */

session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../includes/db.php';
$admin_username = $_SESSION['admin_username'] ?? 'Administrador';

// Obtener todas las secciones
$sections = dbQuery("SELECT * FROM sections ORDER BY id ASC");

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Secciones - HYPE CMS</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="css/dashboard.css">
    
    <style>
        .section-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 2rem;
            align-items: start;
        }

        .section-preview {
            width: 100%;
            height: 200px;
            background: #000;
            border-radius: 8px;
            overflow: hidden;
            position: relative;
            border: 1px solid #333;
        }
        
        .section-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .section-preview .overlay {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            background: rgba(0,0,0,0.7);
            padding: 10px;
            color: #fff;
            font-size: 0.8rem;
        }

        .section-form {
            display: grid;
            gap: 1rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            background: #0F1011;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: #fff;
            font-family: 'Inter', sans-serif;
        }

        .section-header {
            grid-column: 1 / -1;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            border-bottom: 1px solid #333;
            padding-bottom: 0.5rem;
        }

        .section-title-badge {
            background: var(--primary-color);
            color: #000;
            padding: 4px 8px;
            border-radius: 4px;
            font-family: 'Chakra Petch';
            font-weight: bold;
            font-size: 0.8rem;
            text-transform: uppercase;
        }

        .btn-update {
            background: var(--primary-color);
            color: #000;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            justify-self: start;
        }
    </style>
</head>
<body>
    
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-wrapper" id="main-wrapper">
        <?php 
        $page_title = 'Gestor de Secciones';
        include 'includes/topbar.php'; 
        ?>

        <main class="dashboard-content">

            <?php if (isset($_GET['success'])): ?>
                <div style="background: rgba(34, 197, 94, 0.1); color: #4ade80; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
                    ✅ Sección actualizada correctamente.
                </div>
            <?php endif; ?>

            <?php foreach ($sections as $section): ?>
                <form action="actions/section_actions.php" method="POST" enctype="multipart/form-data" class="section-card">
                    <input type="hidden" name="section_id" value="<?php echo $section['id']; ?>">
                    
                    <!-- Columna Izquierda: Preview -->
                    <div>
                        <div class="section-header">
                            <span class="section-title-badge"><?php echo str_replace('_', ' ', strtoupper($section['name'])); ?></span>
                            <small style="color:#666">ID: <?php echo $section['id']; ?></small>
                        </div>

                        <div class="section-preview">
                            <?php if ($section['image_url']): ?>
                                <img src="../<?php echo htmlspecialchars($section['image_url']); ?>" id="preview_<?php echo $section['id']; ?>">
                            <?php else: ?>
                                <div style="display:flex; align-items:center; justify-content:center; height:100%; color:#666;">Sin Imagen</div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group" style="margin-top:1rem;">
                            <label>Cambiar Imagen</label>
                            <input type="file" name="image_file" class="form-control" accept="image/*" onchange="previewImage(this, 'preview_<?php echo $section['id']; ?>')">
                            <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($section['image_url']); ?>">
                        </div>
                    </div>

                    <!-- Columna Derecha: Contenido -->
                    <div class="section-form">
                        
                        <div class="form-group">
                            <label>Título (Admite HTML)</label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($section['title']); ?>">
                        </div>

                        <div class="form-group">
                            <label>Subtítulo / Descripción</label>
                            <textarea name="subtitle" class="form-control" rows="3"><?php echo htmlspecialchars($section['subtitle']); ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Texto Botón</label>
                                <input type="text" name="link_text" class="form-control" value="<?php echo htmlspecialchars($section['link_text']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Enlace Botón</label>
                                <input type="text" name="link_url" class="form-control" value="<?php echo htmlspecialchars($section['link_url']); ?>">
                            </div>
                        </div>

                        <button type="submit" class="btn-update">
                            <i class="ph ph-floppy-disk"></i> Guardar Cambios
                        </button>

                    </div>
                </form>
            <?php endforeach; ?>

        </main>
    </div>

    <script>
        function previewImage(input, imgId) {
            const preview = document.getElementById(imgId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    <script src="js/admin.js"></script>
</body>
</html>
