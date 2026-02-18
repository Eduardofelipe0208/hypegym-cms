<?php
/**
 * Script para ejecutar actualizaciones de base de datos (Historial de Tasas)
 */
require_once 'db.php';

echo "<h1>Actualizando Schema de Tasas...</h1>";

try {
    $db = getDB();
    
    // Leer archivo SQL
    $sql = file_get_contents('database_rates.sql');
    
    // Ejecutar
    $db->exec($sql);
    
    echo "<p>✅ Tabla 'rate_history' creada.</p>";
    echo "<p>✅ Columna 'total_amount_bs' agregada a 'orders'.</p>";
    echo "<p><a href='admin/dashboard.php'>Volver al Dashboard</a></p>";

} catch (PDOException $e) {
    if (strpos($e->getMessage(), "Duplicate column name") !== false) {
        echo "<p style='color:orange'>⚠️ La columna ya existía.</p>";
    } else {
        echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
    }
}
