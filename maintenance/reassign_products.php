<?php
require_once '../includes/db.php';
try {
    // Get new categories
    $cats = dbQuery("SELECT * FROM categories ORDER BY id ASC");
    if (empty($cats)) die("No categories found!");

    $firstCatId = $cats[0]['id'];
    echo "First Category ID: $firstCatId (" . $cats[0]['name'] . ")\n";

    // Update all products to this category
    $rows = dbExecute("UPDATE products SET category_id = ?", [$firstCatId]);
    echo "Updated $rows products to Category ID $firstCatId\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
