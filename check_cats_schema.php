<?php
require_once 'db.php';
try {
    $cats = dbQuery("SELECT * FROM categories LIMIT 1");
    if (empty($cats)) {
        echo "Table is empty or no categories found.\n";
        // Check columns anyway
        $cols = dbQuery("DESCRIBE categories");
        foreach ($cols as $col) {
            echo $col['Field'] . "\n";
        }
    } else {
        echo "Columns:\n";
        print_r(array_keys($cats[0]));
        
        echo "\nAll Categories:\n";
        $all = dbQuery("SELECT * FROM categories");
        print_r($all);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
