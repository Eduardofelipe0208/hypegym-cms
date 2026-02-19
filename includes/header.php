<?php
require_once __DIR__ . '/settings_loader.php';

/**
 * Header Component - HYPE Sportswear
 */

// Valores Dinámicos
$site_logo = getSetting('site_logo_text', 'HYPE');
$primary_color = getSetting('primary_color', '#D6FE00');

// Determinar la página actual para marcar el menú activo
$page = $_GET['page'] ?? 'home';

// Meta tags por defecto si no se especifican
$page_title = $page_title ?? $site_logo . ' | Ropa Deportiva Premium';
$page_description = $page_description ?? 'HYPE Sportswear - Ropa deportiva premium con estilo urbano. Envíos a toda Venezuela. Pago en Bs o USD.';
$og_image = $og_image ?? 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?q=80&w=1200&h=630&fit=crop';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO Meta Tags -->
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta name="keywords" content="ropa deportiva venezuela, streetwear, gym wear, moda urbana, HYPE">
    <meta name="author" content="HYPE Sportswear">

    <!-- Open Graph Meta Tags -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
    
    <title><?php echo htmlspecialchars($page_title); ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚡</text></svg>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@500;600;700&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">

    <!-- Dynamic Styles -->
    <style>
        :root {
            --primary-color: <?php echo $primary_color; ?>;
            --neon-green: <?php echo $primary_color; ?>;
            --text-neon: <?php echo $primary_color; ?>;
            --neon-hover: <?php echo $primary_color; ?>; /* Simplificación por ahora */
        }
    </style>
    <script>
        window.HYPE_CONFIG = {
            currency: "<?php echo isset($currency) ? $currency : '$'; ?>",
            siteName: "<?php echo isset($site_name) ? $site_name : 'HYPE'; ?>"
        };
    </script>
</head>

<body>

    <header class="header">
        <div class="container header__container">
            <a href="index.php" class="logo"><?php echo htmlspecialchars($site_logo); ?><span class="text-neon">.</span></a>
            <nav class="nav">
                <ul class="nav__list">
                    <li><a href="index.php?page=home" class="nav__link <?php echo $page === 'home' ? 'active' : ''; ?>">Home</a></li>
                    <li><a href="index.php?page=shop" class="nav__link <?php echo $page === 'shop' ? 'active' : ''; ?>">Catálogo</a></li>
                    <li><a href="index.php?page=home#collections" class="nav__link">Colecciones</a></li>
                </ul>
            </nav>
            <div class="header__actions">
                <button class="icon-btn" id="cartToggle" aria-label="Abrir carrito">
                    <i class="ph ph-shopping-cart"></i>
                    <span class="cart-badge" id="cartCount">0</span>
                </button>
            </div>
        </div>
    </header>

    <div class="cart-overlay" id="cartOverlay"></div>
    <aside class="cart-drawer" id="cartDrawer">
        <div class="cart-drawer__header">
            <h3>TU PEDIDO <span class="text-neon">HYPE</span></h3>
            <button class="close-btn" id="closeCart"><i class="ph ph-x"></i></button>
        </div>

        <div class="cart-drawer__body" id="cartItems">
            <div class="empty-state">
                <i class="ph ph-shopping-bag"></i>
                <p>Tu carrito está vacío.</p>
                <button class="btn btn--outline btn--sm" onclick="closeCartDrawer()">IR A COMPRAR</button>
            </div>
        </div>

        <div class="cart-drawer__footer">
            <div class="cart-summary">
                <div class="cart-row">
                    <span>Subtotal</span>
                    <span id="cartSubtotal">$0.00</span>
                </div>
                <div class="cart-row total">
                    <span>TOTAL</span>
                    <span class="text-neon" id="cartTotal">$0.00</span>
                </div>
            </div>
            <button class="btn btn--primary btn--full" id="checkoutBtn">
                FINALIZAR PEDIDO POR WHATSAPP <i class="ph ph-whatsapp-logo"></i>
            </button>
            <p class="secure-text"><i class="ph ph-shield-check"></i> Pedido 100% seguro y humano</p>
        </div>
    </aside>
