<?php
/**
 * API Products - HYPE Sportswear CMS
 * Retorna todos los productos en formato JSON para el frontend
 */

header('Content-Type: application/json');
require_once '../db.php';

try {
    // Obtener productos visibles (stock > 0 opcional, pero mejor mostrar todos con estado 'Agotado')
    // Unir con categorías para el filtro
    $sql = "
        SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.is_featured DESC, p.created_at DESC
    ";

    $products = dbQuery($sql);

    // Formatear para el frontend (script.js espera cierta estructura)
    $formatted = array_map(function($p) {
        return [
            'id' => (string)$p['id'], // ID como string para JS
            'name' => $p['name'],
            'price' => floatval($p['price']),
            'category' => $p['category_name'] ? strtolower($p['category_name']) : 'all', // Normalizar
            'image' => $p['image_url'] ? $p['image_url'] : 'https://placehold.co/600x600/1A1B1E/FFF?text=HYPE',
            'badge' => $p['is_featured'] ? 'BEST SELLER' : null, // Lógica simple para badges
            'featured' => (bool)$p['is_featured'],
            'inStock' => $p['stock'] > 0,
            'stock_qty' => (int)$p['stock'],
            'sizes' => $p['sizes'] ? explode(',', $p['sizes']) : ['S','M','L'], // Convertir string a array
            'description' => $p['description']
        ];
    }, $products);

    echo json_encode($formatted);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
