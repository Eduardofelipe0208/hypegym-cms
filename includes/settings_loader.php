<?php
/**
 * Settings Loader - HYPE Sportswear CMS
 * Carga todas las configuraciones globales desde la base de datos
 */

require_once __DIR__ . '/../db.php';

// Obtener todas las configuraciones
$global_settings = [];
try {
    $rows = dbQuery("SELECT * FROM settings");
    foreach ($rows as $r) {
        $global_settings[$r['key']] = $r['value'];
    }
} catch (Exception $e) {
    // Si falla la BD, usar valores por defecto para no romper el sitio
    error_log("Error loading settings: " . $e->getMessage());
}

// Función helper para obtener valores
function getSetting($key, $default = '') {
    global $global_settings;
    return isset($global_settings[$key]) && $global_settings[$key] !== '' 
           ? $global_settings[$key] 
           : $default;
}
?>
