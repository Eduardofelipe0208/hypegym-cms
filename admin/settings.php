<?php
/**
 * Admin Settings - HYPE Sportswear CMS
 * Configuración general y de tasa
 */

session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../includes/db.php';
$admin_username = $_SESSION['admin_username'] ?? 'Administrador';

// Obtener todas las configuraciones
$settings = [];
$rows = dbQuery("SELECT * FROM settings");
foreach ($rows as $r) {
    $settings[$r['key']] = $r['value'];
}

// Obtener historial de tasa (Últimos 10)
$rateHistory = dbQuery("SELECT * FROM rate_history ORDER BY created_at DESC LIMIT 10");

// Obtener métodos de pago
$paymentMethods = dbQuery("SELECT * FROM payment_methods ORDER BY id ASC");

// Helpers
function getVal($key, $default = '') {
    global $settings;
    return htmlspecialchars($settings[$key] ?? $default);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración - HYPE CMS</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="css/dashboard.css">
    
    <style>
        .settings-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }

        .settings-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .section-title {
            font-family: 'Chakra Petch';
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-group { margin-bottom: 1.2rem; }
        .form-label { display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.9rem; }
        
        .form-control, .color-picker {
            width: 100%;
            padding: 10px;
            background: #0F1011;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: #fff;
            font-family: 'Inter', sans-serif;
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }
        .history-table td {
            padding: 8px 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            color: var(--text-muted);
        }
        .history-table tr:last-child td { border-bottom: none; }
        .rate-change { color: #fff; font-weight: 600; }

        .btn-save {
            background: var(--primary-color);
            color: #000;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
        }
    </style>
</head>
<body>
    
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-wrapper" id="main-wrapper">
        <?php 
        $page_title = 'Configuración del Sistema';
        include 'includes/topbar.php'; 
        ?>

        <main class="dashboard-content">

            <?php if (isset($_GET['success'])): ?>
                <div style="background: rgba(34, 197, 94, 0.1); color: #4ade80; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    ✅ Configuración guardada correctamente.
                </div>
            <?php endif; ?>
            
            <form action="actions/settings_actions.php" method="POST" class="settings-grid" enctype="multipart/form-data">
                
                <!-- Columna Izquierda: Configuración General -->
                <div class="left-col">
                    
                    <!-- Branding -->
                    <div class="settings-card">
                        <h3 class="section-title"><i class="ph ph-paint-brush"></i> Identidad Visual</h3>
                        
                        <div class="form-group">
                            <label class="form-label">Texto del Logo</label>
                            <input type="text" name="site_logo_text" class="form-control" value="<?php echo getVal('site_logo_text', 'HYPE'); ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Nombre de la Tienda</label>
                            <input type="text" name="site_name" class="form-control" value="<?php echo getVal('site_name', 'HYPE Sportswear'); ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Símbolo de Moneda</label>
                            <input type="text" name="currency_symbol" class="form-control" value="<?php echo getVal('currency_symbol', '$'); ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Color Principal (Neon Accent)</label>
                            <div style="display:flex; gap:10px;">
                                <input type="color" name="primary_color" class="color-picker" style="width:60px; height:40px; padding:0;" value="<?php echo getVal('primary_color', '#D6FE00'); ?>">
                                <input type="text" class="form-control" value="<?php echo getVal('primary_color', '#D6FE00'); ?>" readonly style="flex:1;">
                            </div>
                        </div>
                    </div>

                    <!-- (Banner Principal movido a Secciones) -->

                    <!-- Contacto & Redes -->
                    <div class="settings-card">
                        <h3 class="section-title"><i class="ph ph-share-network"></i> Contacto y Redes</h3>
                        <div class="form-group">
                            <label class="form-label">Whatsapp Pedidos</label>
                            <input type="text" name="whatsapp_number" class="form-control" value="<?php echo getVal('whatsapp_number'); ?>" placeholder="Ej: 584121234567">
                        </div>
                        <div class="form-row" style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                            <div class="form-group">
                                <label class="form-label">Instagram URL</label>
                                <input type="text" name="social_instagram" class="form-control" value="<?php echo getVal('social_instagram'); ?>" placeholder="https://instagram.com/...">
                            </div>
                            <div class="form-group">
                                <label class="form-label">TikTok URL</label>
                                <input type="text" name="social_tiktok" class="form-control" value="<?php echo getVal('social_tiktok'); ?>" placeholder="https://tiktok.com/...">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-save">
                        <i class="ph ph-floppy-disk"></i> Guardar Cambios
                    </button>
                </div>

                <!-- Columna Derecha: Finanzas -->
                <div class="right-col">
                    
                    <!-- Tasa BCV -->
                    <div class="settings-card">
                        <h3 class="section-title"><i class="ph ph-currency-dollar"></i> Tasa de Cambio</h3>
                        
                        <div class="form-group">
                            <label class="form-label">Tasa Actual (Bs/USD)</label>
                            <input type="number" step="0.01" name="tasa_bcv" class="form-control" style="font-size:1.5rem; font-weight:bold; color:var(--primary-color);" value="<?php echo getVal('tasa_bcv', '36.50'); ?>">
                            <p style="font-size:0.8rem; color:#666; margin-top:5px;">Al cambiar este valor, se registrará en el historial.</p>
                        </div>

                        <h4 style="font-size:0.9rem; margin-bottom:10px; color:#fff;">Historial de Cambios</h4>
                        <table class="history-table">
                            <?php foreach($rateHistory as $h): ?>
                            <tr>
                                <td><?php echo date('d/m H:i', strtotime($h['created_at'])); ?></td>
                                <td class="rate-change">Bs. <?php echo number_format($h['rate'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>

                    <!-- Métodos de Pago -->
                    <div class="settings-card">
                        <h3 class="section-title"><i class="ph ph-credit-card"></i> Métodos de Pago</h3>
                        <p style="font-size:0.85rem; color:var(--text-muted); margin-bottom:1rem;">
                            Configura los datos bancarios que verá el cliente al finalizar la compra.
                        </p>
                        
                        <?php foreach($paymentMethods as $pm): ?>
                        <div style="background:rgba(255,255,255,0.03); padding:1rem; border-radius:8px; margin-bottom:1rem; border:1px solid var(--border-color);">
                            <div style="display:flex; justify-content:space-between; margin-bottom:0.5rem;">
                                <input type="hidden" name="payment_methods[<?php echo $pm['id']; ?>][id]" value="<?php echo $pm['id']; ?>">
                                <input type="text" name="payment_methods[<?php echo $pm['id']; ?>][name]" value="<?php echo htmlspecialchars($pm['name']); ?>" class="form-control" style="font-weight:bold; width:60%;">
                                
                                <label style="display:flex; align-items:center; gap:5px; cursor:pointer;">
                                    <input type="checkbox" name="payment_methods[<?php echo $pm['id']; ?>][is_active]" value="1" <?php echo $pm['is_active'] ? 'checked' : ''; ?>>
                                    <span style="font-size:0.85rem;">Activo</span>
                                </label>
                            </div>
                            <textarea name="payment_methods[<?php echo $pm['id']; ?>][instructions]" class="form-control" rows="3" placeholder="Ingresa los datos bancarios (Banco, Cuenta, CI, Tlf, Correo)..."><?php echo htmlspecialchars($pm['instructions']); ?></textarea>
                        </div>
                        <?php endforeach; ?>
                    </div>

                </div>
            </form>

        </main>
    </div>
    <script>
        function previewBanner(input) {
            const preview = document.getElementById('bannerPreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    <script src="js/admin.js"></script>
</body>
</html>
