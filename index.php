<?php
/**
 * Front Controller - HYPE Sportswear CMS
 * Maneja todas las solicitudes y carga el tema correspondiente
 */

// 1. Cargar configuración global (si existe)
if (file_exists('includes/db.php')) {
    require_once 'includes/settings_loader.php';
} else {
    // Si no hay configuración, redirigir al instalador
    if (file_exists('install.php')) {
        header('Location: install.php');
        exit;
    } else {
        die("Error: El sistema no est&aacute; instalado y no se encuentra el instalador.");
    }
}

// 2. Definir rutas válidas y sus archivos de tema correspondientes
$routes = [
    'home'    => 'home.php',
    'shop'    => 'shop.php',
    'product' => 'product.php',
    'cart'    => 'cart.php', // Futuro
    'checkout'=> 'checkout.php' // Futuro
];

// 3. Detectar la página solicitada
// Opción A: index.php?page=nombre (Más compatible con servidor local simple)
$page = $_GET['page'] ?? 'home';

// Validar que la página exista en nuestras rutas
if (!array_key_exists($page, $routes)) {
    // Si no existe, 404 o redirigir a home
    http_response_code(404);
    $page = 'home'; // Fallback a home por ahora
}

// 4. Configurar el tema (por defecto 'default')
// En el futuro esto vendrá de la base de datos: getSetting('active_theme', 'default')
$theme = 'default'; 
$themePath = "themes/$theme/";

// Archivo a cargar
$themeFile = $themePath . $routes[$page];

// 5. Verificar que el archivo del tema exista
if (!file_exists($themeFile)) {
    die("Error: El archivo del tema '$themeFile' no existe.");
}

// 6. Inyectar variables globales para el tema (Simulación por ahora)
// Estas vendrán de la DB en el paso 1.3
$site_name = getSetting('site_name', 'HYPE Sportswear');
$currency = getSetting('currency_symbol', '$');

// 7. Cargar el tema
include $themeFile;
