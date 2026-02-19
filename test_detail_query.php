<?php
require 'includes/db.php';
$orderId = 7;
try {
    $items = dbQuery("
        SELECT oi.*, p.name as current_name, p.image 
        FROM order_items oi 
        LEFT JOIN products p ON oi.product_id = p.id 
        WHERE oi.order_id = ?", 
        [$orderId]
    );
    echo "Count: " . count($items) . "\n";
    print_r($items);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
