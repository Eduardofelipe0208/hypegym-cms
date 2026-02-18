<?php
// Meta tags personalizados para la página de catálogo
$page_title = 'Catálogo | HYPE Sportswear';
$page_description = 'Explora nuestro catálogo completo de ropa deportiva. Calidad premium, diseños únicos. Envíos a toda Venezuela.';

include 'includes/header.php';
?>

<main>
    <section class="page-header">
        <div class="container">
            <h1 class="page-title">CATÁLOGO <span class="text-neon">ONLINE</span></h1>
            <p>Equipamiento diseñado para dominar.</p>
        </div>
    </section>

    <section class="section shop-section">
        <div class="container">
            <!-- Buscador en tiempo real -->
            <div class="search-bar">
                <i class="ph ph-magnifying-glass"></i>
                <input type="text" id="searchInput" class="search-input" placeholder="Buscar productos..."
                    autocomplete="off">
            </div>

            <div class="filter-bar">
                <button class="filter-btn active" data-category="all">TODO</button>
                <button class="filter-btn" data-category="gym wear">GYM WEAR</button>
                <button class="filter-btn" data-category="street">STREET</button>
                <button class="filter-btn" data-category="accesorios">ACCESORIOS</button>
            </div>
            <div class="grid-products shop-grid" id="shopContainer">
                <p style="color:#666">Cargando productos...</p>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
