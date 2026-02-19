<?php
require 'includes/db.php';

try {
    echo "Adding size column to order_items...\n";
    $sql = "ALTER TABLE order_items ADD COLUMN size VARCHAR(20) DEFAULT '' AFTER product_name";
    dbExecute($sql);
    echo "Column added successfully.\n";
} catch (Exception $e) {
    echo "Error (maybe already exists): " . $e->getMessage() . "\n";
}
