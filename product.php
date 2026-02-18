<?php
/**
 * Página de Detalle de Producto - HYPE Sportswear
 * IMPORTANTE: Consulta la BD para generar meta tags dinámicos para WhatsApp
 */

// Incluir conexión a la base de datos
require_once 'includes/db.php';

// Obtener el ID del producto desde la URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Variables por defecto en caso de que no se encuentre el producto
$page_title = 'Producto | HYPE Sportswear';
$page_description = 'Descubre nuestros productos deportivos premium.';
$og_image = 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?q=80&w=1200&h=630&fit=crop';

// Si hay un ID válido, consultar el producto en la base de datos
if ($product_id > 0) {
    try {
        // Consultar producto específico con JOIN a categorías
        $sql = "
            SELECT 
                p.id,
                p.name,
                p.description,
                p.price,
                c.name AS category_name,
                p.image_url,
                p.stock
            FROM products p
            INNER JOIN categories c ON p.category_id = c.id
            WHERE p.id = ?
        ";
        
        $product = dbQueryOne($sql, [$product_id]);
        
        // Si se encuentra el producto, personalizar los meta tags
        if ($product) {
            $page_title = htmlspecialchars($product['name']) . ' | HYPE Sportswear';
            $page_description = htmlspecialchars($product['description']) . ' - $' . number_format($product['price'], 2);
            
            // Usar la imagen del producto si existe, sino usar la por defecto
            if (!empty($product['image_url'])) {
                // Si la imagen es relativa, convertirla a absoluta
                $image_url = $product['image_url'];
                if (!filter_var($image_url, FILTER_VALIDATE_URL)) {
                    // Construir URL absoluta (ajustar según tu dominio en producción)
                    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                    $host = $_SERVER['HTTP_HOST'];
                    $og_image = $protocol . '://' . $host . '/' . ltrim($image_url, '/');
                } else {
                    $og_image = $image_url;
                }
            }
        }
    } catch (Exception $e) {
        // En caso de error, usar valores por defecto
        error_log("Error al obtener producto: " . $e->getMessage());
    }
}

// Incluir el header con los meta tags personalizados
include 'includes/header.php';
?>

<main class="main-product">
    <div class="container" id="product-detail-container">
        <p style="text-align:center; padding: 4rem; color: #666;">Cargando detalles del producto...</p>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
