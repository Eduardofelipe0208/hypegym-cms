<?php
// No requiere meta tags personalizados, usar los por defecto
include 'includes/header.php';
?>

<main>
    <section class="hero">
        <div class="hero__bg">
            <img src="<?php echo htmlspecialchars(getSetting('hero_image', 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?q=80&w=1470&auto=format&fit=crop')); ?>"
                alt="Entrenamiento HYPE">
            <div class="overlay"></div>
        </div>
        <div class="container hero__content">
            <span class="hero__tag">NEW COLLECTION 2026</span>
            <h1 class="hero__title"><?php echo getSetting('hero_title', 'SPORTSWEAR PARA LOS QUE <span class="text-neon">NO SE DETIENEN</span>'); ?></h1>
            <p class="hero__subtitle"><?php echo htmlspecialchars(getSetting('hero_subtitle', 'Diseño urbano, rendimiento profesional. HYPE no es moda, es mentalidad.')); ?></p>
            <div class="hero__btns">
                <a href="shop.php" class="btn btn--primary">VER TIENDA</a>
                <a href="#collections" class="btn btn--outline">VER COLECCIONES</a>
            </div>
        </div>
    </section>

    <section id="collections" class="section">
        <div class="container">
            <h2 class="section-title">COLECCIONES</h2>
            <div class="grid-collections">
                <a href="shop.php?category=gym%20wear" class="collection-card">
                    <img src="https://images.unsplash.com/photo-1581009146145-b5ef050c2e1e?q=80&w=1470&auto=format&fit=crop"
                        alt="Gym">
                    <div class="collection-content">
                        <h3>GYM WEAR</h3>
                    </div>
                </a>
                <a href="shop.php?category=street" class="collection-card">
                    <img src="img/4.jpg" alt="Street">
                    <div class="collection-content">
                        <h3>STREET</h3>
                    </div>
                </a>
                <a href="shop.php?category=accesorios" class="collection-card">
                    <img src="https://images.unsplash.com/photo-1517836357463-d25dfeac3438?q=80&w=1470&auto=format&fit=crop"
                        alt="Performance">
                    <div class="collection-content">
                        <h3>ACCESORIOS</h3>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <section class="section section--dark">
        <div class="container">
            <h2 class="section-title">LO MÁS <span class="text-neon">VENDIDO</span></h2>
            <!-- Productos renderizados dinámicamente vía JS -->
            <div class="grid-products" id="featuredContainer"></div>

            <div class="text-center" style="margin-top: 3rem;">
                <a href="shop.php" class="btn btn--outline">VER TODO EL CATÁLOGO</a>
            </div>
        </div>
    </section>

    <section class="section benefits-section">
        <div class="container grid-benefits">
            <div class="benefit">
                <i class="ph ph-lightning text-neon"></i>
                <h4>ENERGÍA</h4>
                <p>Diseños para romper tus límites.</p>
            </div>
            <div class="benefit">
                <i class="ph ph-medal text-neon"></i>
                <h4>CALIDAD</h4>
                <p>Telas premium anti-transpirantes.</p>
            </div>
            <div class="benefit">
                <i class="ph ph-truck text-neon"></i>
                <h4>ENVÍOS</h4>
                <p>Rápidos y seguros a todo el país.</p>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
