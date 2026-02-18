<?php
/**
 * Logger Helper - HYPE Sportswear CMS
 * Función global para registrar actividades
 */

require_once __DIR__ . '/../../includes/db.php';

if (!function_exists('logAction')) {
    function logAction($action, $description = '') {
        try {
            // Obtener ID de usuario si hay sesión
            $userId = $_SESSION['user_id'] ?? null;
            
            // Obtener IP
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

            $db = getDB();
            $sql = "INSERT INTO logs (user_id, action, description, ip_address) VALUES (?, ?, ?, ?)";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$userId, $action, $description, $ip]);

        } catch (Exception $e) {
            // Silencioso: Si falla el log, no detener la aplicación
            error_log("Error al registrar log: " . $e->getMessage());
        }
    }
}
