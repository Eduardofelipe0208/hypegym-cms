<?php
require_once 'includes/db.php';
header('Content-Type: text/plain');

try {
    $db = getDB();
    
    echo "--- CHECKING TABLES ---\n";
    $tables = dbQuery("SHOW TABLES");
    print_r($tables);

    echo "\n--- DESCRIBE ORDERS ---\n";
    $cols = dbQuery("DESCRIBE orders");
    print_r($cols);

    echo "\n--- DESCRIBE PAYMENT_METHODS ---\n";
    $cols2 = dbQuery("DESCRIBE payment_methods");
    print_r($cols2);

    echo "\n--- CHECKING ORDER ITEMS FOR ORDER 6 ---\n";
    $sql = "
        SELECT 
            oi.order_id,
            oi.product_id,
            oi.price,
            p.name as product_name,
            p.id as real_product_id
        FROM order_items oi 
        LEFT JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = 6
    ";
    $res = dbQuery($sql);
    print_r($res);

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
