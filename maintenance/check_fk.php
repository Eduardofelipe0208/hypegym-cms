<?php
require_once __DIR__ . '/../includes/db.php';
try {
    $res = dbQuery("SHOW CREATE TABLE order_items");
    print_r($res);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
