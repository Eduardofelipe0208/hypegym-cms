<?php
require_once 'db.php';
try {
    // 1. Truncate categories (WARNING: IDs will reset, but we only have 3 so it's fine)
    // Actually, let's just delete all and insert new ones to get clean IDs 1, 2, 3
    dbExecute("DELETE FROM categories");
    dbExecute("ALTER TABLE categories AUTO_INCREMENT = 1"); // Reset ID if possible, or just insert

    $cats = ['GYM WEAR', 'STREET', 'ACCESORIOS'];
    foreach ($cats as $name) {
        dbExecute("INSERT INTO categories (name) VALUES (?)", [$name]);
    }
    
    echo "Categories updated:\n";
    $newCats = dbQuery("SELECT * FROM categories");
    print_r($newCats);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
