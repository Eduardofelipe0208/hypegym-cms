<?php
/**
 * Script para ejecutar actualizaciones de base de datos (Logs)
 */
require_once '../includes/db.php';

echo "<h1>Creando Tabla de Logs...</h1>";

try {
    $db = getDB();
    
    // Leer archivo SQL
    $sql = file_get_contents('database_logs.sql');
    
    // Ejecutar
    $db->exec($sql);
    
    echo "<p>✅ Tabla 'logs' creada exitosamente.</p>";
    echo "<p><a href='admin/dashboard.php'>Volver al Dashboard</a></p>";

} catch (PDOException $e) {
    if (strpos($e->getMessage(), "already exists") !== false) {
        echo "<p style='color:orange'>⚠️ La tabla ya existía.</p>";
    } else {
        echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
    }
}
