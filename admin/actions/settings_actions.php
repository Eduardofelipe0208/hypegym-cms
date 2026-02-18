<?php
/**
 * Settings Actions - HYPE Sportswear CMS
 * Guarda configuraciones y registra historial de tasa
 */

session_start();
require_once '../../includes/db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = getDB();
    
    try {
        $db->beginTransaction();

        // 1. Detectar cambio de tasa antes de guardar
        $newRate = floatval($_POST['tasa_bcv']);
        $oldRateQuery = dbQueryOne("SELECT value FROM settings WHERE `key` = 'tasa_bcv'");
        $oldRate = $oldRateQuery ? floatval($oldRateQuery['value']) : 0;

        // Si la tasa cambió, registrar en historial
        if (abs($newRate - $oldRate) > 0.001) {
            $sqlHistory = "INSERT INTO rate_history (rate, user_id) VALUES (?, ?)";
            // Asumimos que tenemos $_SESSION['user_id'], si no, usamos NULL o 1 por defecto
            $userId = $_SESSION['user_id'] ?? 1; 
            dbExecute($sqlHistory, [$newRate, $userId]);
        }

        // 1.5 Manejo de subida de imagen (Banner)
        if (isset($_FILES['hero_image_file']) && $_FILES['hero_image_file']['error'] === 0) {
            $uploadDir = '../../img/uploads/';
            if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

            $fileName = 'hero_' . time() . '_' . basename($_FILES['hero_image_file']['name']);
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['hero_image_file']['tmp_name'], $targetPath)) {
                // Guardar ruta relativa en DB
                $dbPath = 'img/uploads/' . $fileName;
                
                // Actualizar o insertar
                $exists = dbQueryOne("SELECT id FROM settings WHERE `key` = 'hero_image'");
                if ($exists) {
                    dbExecute("UPDATE settings SET `value` = ? WHERE `key` = 'hero_image'", [$dbPath]);
                } else {
                    dbExecute("INSERT INTO settings (`key`, `value`) VALUES ('hero_image', ?)", [$dbPath]);
                }
            }
        }

        // 2. Guardar todas las configuraciones enviadas
        $allowedKeys = [
            'site_logo_text', 
            'primary_color', 
            'hero_title', 
            'hero_subtitle',
            // 'hero_image' se maneja arriba
            'whatsapp_number',
            'social_instagram',
            'social_tiktok',
            'tasa_bcv'
        ];

        foreach ($allowedKeys as $key) {
            if (isset($_POST[$key])) {
                $val = $_POST[$key];
                // Check if exists
                $exists = dbQueryOne("SELECT id FROM settings WHERE `key` = ?", [$key]);
                if ($exists) {
                    $sql = "UPDATE settings SET `value` = ? WHERE `key` = ?";
                    dbExecute($sql, [$val, $key]);
                } else {
                    $sql = "INSERT INTO settings (`key`, `value`) VALUES (?, ?)";
                    dbExecute($sql, [$key, $val]);
                }
            }
        }

        
        $db->commit();
        
        // Agregar logs
        require_once '../includes/logger.php';
        logAction('config_actualizada', 'Se actualizaron las configuraciones del sistema');
        
        header('Location: ../settings.php?success=saved');

    } catch (Exception $e) {
        $db->rollBack();
        // Log error
        file_put_contents('error_log.txt', date('Y-m-d H:i:s') . " - Settings Error: " . $e->getMessage() . "\n", FILE_APPEND);
        header('Location: ../settings.php?error=' . urlencode($e->getMessage()));
    }
}
