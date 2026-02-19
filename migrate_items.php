<?php
require_once 'includes/db.php';
header('Content-Type: text/plain');

try {
    $db = getDB();
    
    // 1. Add column if not exists
    $cols = dbQuery("DESCRIBE order_items");
    $hasCol = false;
    foreach($cols as $c) {
        if($c['Field'] === 'product_name') $hasCol = true;
    }

    if(!$hasCol) {
        echo "Adding product_name column...\n";
        dbExecute("ALTER TABLE order_items ADD COLUMN product_name VARCHAR(255) AFTER product_id");
        echo "Column added.\n";
    } else {
        echo "Column already exists.\n";
    }

    // 2. Try to populate existing items (Best Effort)
    echo "Populating existing items...\n";
    dbExecute("
        UPDATE order_items oi
        JOIN products p ON oi.product_id = p.id
        SET oi.product_name = p.name
        WHERE oi.product_name IS NULL OR oi.product_name = ''
    ");
    echo "Done.";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
