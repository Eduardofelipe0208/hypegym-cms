<?php
/**
 * API Config - HYPE Sportswear CMS
 * Retorna configuraciones públicas (tasa, métodos de pago, colores)
 */

header('Content-Type: application/json');
require_once '../includes/db.php';

try {
    $db = getDB();
    $response = [];

    // 1. Tasa de Cambio
    $tasaQuery = dbQueryOne("SELECT value FROM settings WHERE `key` = 'tasa_bcv'");
    $response['exchange_rate'] = $tasaQuery ? floatval($tasaQuery['value']) : 36.50;

    // 2. Métodos de Pago Activos
    $methods = dbQuery("SELECT id, name, description, instructions FROM payment_methods WHERE is_active = TRUE ORDER BY name ASC");
    $response['payment_methods'] = $methods;

    // 3. Configuración Visual (CMS)
    $settingsKeys = ['site_logo_text', 'primary_color', 'hero_title', 'hero_subtitle', 'whatsapp_number'];
    $placeholders = implode(',', array_fill(0, count($settingsKeys), '?'));
    
    $settings = dbQuery("SELECT `key`, `value` FROM settings WHERE `key` IN ($placeholders)", $settingsKeys);
    
    foreach ($settings as $s) {
        $response['settings'][$s['key']] = $s['value'];
    }

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
