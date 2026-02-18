<?php
/**
 * Script para ejecutar actualizaciones de base de datos (Checkout & CMS)
 */
require_once 'db.php';

echo "<h1>Actualizando Base de Datos (Checkout + CMS)...</h1>";

try {
    $db = getDB();
    
    // Leer archivo SQL
    $sql = file_get_contents('database_checkout.sql');
    
    // Ejecutar múltiples queries
    $db->exec($sql);
    
    echo "<p>✅ Tabla 'orders' actualizada con campos de pago.</p>";
    echo "<p>✅ Tabla 'testimonials' creada.</p>";
    echo "<p>✅ Configuraciones CMS insertadas.</p>";
    echo "<p><a href='admin/dashboard.php'>Volver al Dashboard</a></p>";

} catch (PDOException $e) {
    // Si el error es "Duplicate column", es porque ya se corrió, no pasa nada grave
    if (strpos($e->getMessage(), "Duplicate column name") !== false) {
        echo "<p style='color:orange'>⚠️ Algunas columnas ya existían (Script re-ejecutado).</p>";
    } else {
        echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
    }
}
