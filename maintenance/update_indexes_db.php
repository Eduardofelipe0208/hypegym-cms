<?php
/**
 * Script para ejecutar optimizaciones de base de datos
 */
require_once '../includes/db.php';

echo "<h1>Aplicando Índices...</h1>";

try {
    $db = getDB();
    
    // Leer archivo SQL
    $sql = file_get_contents('database_indexes.sql');
    
    // Ejecutar
    $db->exec($sql);
    
    echo "<p>✅ Índices creados exitosamente en tabla 'orders'.</p>";
    echo "<p><a href='admin/dashboard.php'>Volver al Dashboard</a></p>";

} catch (PDOException $e) {
    if (strpos($e->getMessage(), "Duplicate key name") !== false) {
        echo "<p style='color:orange'>⚠️ Los índices ya existían.</p>";
    } else {
        echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
    }
}
