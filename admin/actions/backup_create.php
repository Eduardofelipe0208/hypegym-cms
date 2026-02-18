<?php
/**
 * Backup Create - HYPE Sportswear CMS
 * Genera un dump completo de la base de datos
 */

session_start();
require_once '../../db.php';
require_once '../includes/logger.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login.php');
    exit;
}

// Configuración básica
$backupDir = __DIR__ . '/../backups/';
if (!file_exists($backupDir)) {
    mkdir($backupDir, 0755, true);
}

$filename = 'backup_hype_' . date('Y-m-d_H-i-s') . '.sql';
$filepath = $backupDir . $filename;

try {
    $db = getDB();
    $tables = [];
    
    // Obtener todas las tablas
    $result = $db->query("SHOW TABLES");
    while ($row = $result->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }

    $sqlScript = "-- HYPE CMS Database Backup\n";
    $sqlScript .= "-- Fecha: " . date('Y-m-d H:i:s') . "\n\n";
    $sqlScript .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

    foreach ($tables as $table) {
        // Estructura
        $row = $db->query("SHOW CREATE TABLE $table")->fetch(PDO::FETCH_NUM);
        $sqlScript .= "\n\n" . $row[1] . ";\n\n";

        // Datos
        $rows = $db->query("SELECT * FROM $table");
        $numFields = $rows->columnCount();

        while ($row = $rows->fetch(PDO::FETCH_NUM)) {
            $sqlScript .= "INSERT INTO $table VALUES(";
            for ($j = 0; $j < $numFields; $j++) {
                $row[$j] = addslashes($row[$j]);
                $row[$j] = str_replace("\n", "\\n", $row[$j]);
                if (isset($row[$j])) {
                    $sqlScript .= '"' . $row[$j] . '"';
                } else {
                    $sqlScript .= '""';
                }
                if ($j < ($numFields - 1)) {
                    $sqlScript .= ',';
                }
            }
            $sqlScript .= ");\n";
        }
    }

    $sqlScript .= "\n\nSET FOREIGN_KEY_CHECKS=1;";

    // Guardar archivo
    file_put_contents($filepath, $sqlScript);

    // Guardar log
    logAction('backup_creado', "Backup generado: $filename");

    header('Location: ../backups.php?success=created');

} catch (Exception $e) {
    header('Location: ../backups.php?error=' . urlencode($e->getMessage()));
}
