<?php
/**
 * Backup Download - HYPE Sportswear CMS
 * Descarga segura de archivos SQL
 */

session_start();
require_once '../includes/logger.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    die('Acceso denegado');
}

$file = $_GET['file'] ?? '';
$backupDir = __DIR__ . '/../backups/';

// Validación de seguridad para evitar Path Traversal (../)
if (basename($file) !== $file || empty($file)) {
    die('Nombre de archivo inválido');
}

$filepath = $backupDir . $file;

if (file_exists($filepath)) {
    // Log
    logAction('backup_descargado', "Backup descargado: $file");

    // Headers para forzar descarga
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filepath));
    readfile($filepath);
    exit;
} else {
    die('Archivo no encontrado');
}
