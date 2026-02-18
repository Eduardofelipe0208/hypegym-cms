<?php
/**
 * Script para ejecutar actualizaciones de base de datos
 */
require_once '../includes/db.php';

echo "<h1>Actualizando Base de Datos...</h1>";

try {
    $db = getDB();
    
    // Leer archivo SQL
    $sql = file_get_contents('database_updates.sql');
    
    // Ejecutar múltiples queries
    $db->exec($sql);
    
    echo "<p>✅ Tablas 'orders' y 'order_items' creadas/verificadas.</p>";
    echo "<p>✅ Datos de prueba insertados correctamente.</p>";
    echo "<p><a href='admin/dashboard.php'>Ir al Dashboard</a></p>";

} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
}
