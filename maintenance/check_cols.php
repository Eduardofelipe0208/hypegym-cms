<?php
require_once '../includes/db.php';
try {
    $cols = dbQuery("SHOW COLUMNS FROM categories");
    foreach ($cols as $col) {
        echo $col['Field'] . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
