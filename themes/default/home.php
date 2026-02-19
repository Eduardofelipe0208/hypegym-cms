<?php
// Obtener datos de secciones
$sections_data = [];
try {
    $rows = dbQuery("SELECT * FROM sections");
    foreach ($rows as $r) {
        $sections_data[$r['name']] = $r;
    }
} catch (Exception $e) {
    // Silencioso en frontend
}

function getSec($name, $field, $default = '') {
    global $sections_data;
    return isset($sections_data[$name][$field]) && $sections_data[$name][$field] !== '' 
           ? $sections_data[$name][$field] 
           : $default;
}

include 'includes/header.php';
?>

<main>
    <section class="hero">
        <div class="hero__bg">
            <img src="<?php echo htmlspecialchars(getSec('home_hero', 'image_url', 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?q=80&w=1470&auto=format&fit=crop')); ?>"
                alt="Hero Background">
            <div class="overlay"></div>
        </div>
        <div class="container hero__content">
            <span class="hero__tag">NEW COLLECTION <?php echo date('Y'); ?></span>
            <h1 class="hero__title"><?php echo getSec('home_hero', 'title', 'HYPE SPORTSWEAR'); ?></h1>
            <p class="hero__subtitle"><?php echo htmlspecialchars(getSec('home_hero', 'subtitle', 'Premium Athletic Wear')); ?></p>
            <div class="hero__btns">
                <a href="<?php echo htmlspecialchars(getSec('home_hero', 'link_url', 'index.php?page=shop')); ?>" class="btn btn--primary">
                    <?php echo htmlspecialchars(getSec('home_hero', 'link_text', 'VER TIENDA')); ?>
                </a>
                <a href="#collections" class="btn btn--outline">VER COLECCIONES</a>
            </div>
        </div>
    </section>

    <section id="collections" class="section">
        <div class="container">
            <h2 class="section-title">COLECCIONES</h2>
            <div class="grid-collections">
                <!-- GYM -->
                <a href="<?php echo htmlspecialchars(getSec('collection_gym', 'link_url', 'index.php?page=shop&category=gym%20wear')); ?>" class="collection-card">
                    <img src="<?php echo htmlspecialchars(getSec('collection_gym', 'image_url', 'https://images.unsplash.com/photo-1581009146145-b5ef050c2e1e?q=80&w=1470&auto=format&fit=crop')); ?>"
                        alt="<?php echo htmlspecialchars(getSec('collection_gym', 'title', 'GYM')); ?>">
                    <div class="collection-content">
                        <h3><?php echo htmlspecialchars(getSec('collection_gym', 'title', 'GYM WEAR')); ?></h3>
                    </div>
                </a>
                <!-- STREET -->
                <a href="<?php echo htmlspecialchars(getSec('collection_street', 'link_url', 'index.php?page=shop&category=street')); ?>" class="collection-card">
                    <img src="<?php echo htmlspecialchars(getSec('collection_street', 'image_url', 'img/4.jpg')); ?>" alt="Street">
                    <div class="collection-content">
                        <h3><?php echo htmlspecialchars(getSec('collection_street', 'title', 'STREET')); ?></h3>
                    </div>
                </a>
                <!-- ACCESSORIES -->
                <a href="<?php echo htmlspecialchars(getSec('collection_accessories', 'link_url', 'index.php?page=shop&category=accesorios')); ?>" class="collection-card">
                    <img src="<?php echo htmlspecialchars(getSec('collection_accessories', 'image_url', 'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?q=80&w=1470&auto=format&fit=crop')); ?>"
                        alt="Performance">
                    <div class="collection-content">
                        <h3><?php echo htmlspecialchars(getSec('collection_accessories', 'title', 'ACCESORIOS')); ?></h3>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <section class="section section--dark">
        <div class="container">
            <h2 class="section-title"><?php echo getSec('home_bestsellers', 'title', 'LO MÁS <span class="text-neon">VENDIDO</span>'); ?></h2>
            <p class="text-center" style="color:#888; margin-top:-1rem; margin-bottom:2rem;"><?php echo htmlspecialchars(getSec('home_bestsellers', 'subtitle', '')); ?></p>
            
            <!-- Productos renderizados dinámicamente vía JS -->
            <div class="grid-products" id="featuredContainer"></div>

            <div class="text-center" style="margin-top: 3rem;">
                <a href="<?php echo htmlspecialchars(getSec('home_bestsellers', 'link_url', 'index.php?page=shop')); ?>" class="btn btn--outline">
                    <?php echo htmlspecialchars(getSec('home_bestsellers', 'link_text', 'VER TODO EL CATÁLOGO')); ?>
                </a>
            </div>
        </div>
    </section>

    <section class="section benefits-section">
        <div class="container grid-benefits">
            <!-- Benefit 1 -->
            <div class="benefit">
                <i class="ph <?php echo htmlspecialchars(getSec('benefit_energy', 'link_text', 'ph-lightning')); ?> text-neon"></i>
                <h4><?php echo htmlspecialchars(getSec('benefit_energy', 'title', 'ENERGÍA')); ?></h4>
                <p><?php echo htmlspecialchars(getSec('benefit_energy', 'subtitle', 'Diseños para romper tus límites.')); ?></p>
            </div>
            <!-- Benefit 2 -->
            <div class="benefit">
                <i class="ph <?php echo htmlspecialchars(getSec('benefit_quality', 'link_text', 'ph-medal')); ?> text-neon"></i>
                <h4><?php echo htmlspecialchars(getSec('benefit_quality', 'title', 'CALIDAD')); ?></h4>
                <p><?php echo htmlspecialchars(getSec('benefit_quality', 'subtitle', 'Telas premium anti-transpirantes.')); ?></p>
            </div>
            <!-- Benefit 3 -->
            <div class="benefit">
                <i class="ph <?php echo htmlspecialchars(getSec('benefit_shipping', 'link_text', 'ph-truck')); ?> text-neon"></i>
                <h4><?php echo htmlspecialchars(getSec('benefit_shipping', 'title', 'ENVÍOS')); ?></h4>
                <p><?php echo htmlspecialchars(getSec('benefit_shipping', 'subtitle', 'Rápidos y seguros a todo el país.')); ?></p>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
