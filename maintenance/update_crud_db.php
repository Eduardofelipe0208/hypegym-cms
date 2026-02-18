<?php
/**
 * Script para ejecutar actualizaciones de base de datos para CRUD
 */
require_once '../includes/db.php';

echo "<h1>Actualizando Base de Datos (CRUD)...</h1>";

try {
    $db = getDB();
    
    // Leer archivo SQL
    $sql = file_get_contents('database_crud.sql');
    
    // Ejecutar múltiples queries
    $db->exec($sql);
    
    echo "<p>✅ Tabla 'payment_methods' creada/verificada.</p>";
    echo "<p>✅ Datos de prueba insertados correctamente.</p>";
    echo "<p><a href='admin/dashboard.php'>Volver al Dashboard</a></p>";

} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
}
