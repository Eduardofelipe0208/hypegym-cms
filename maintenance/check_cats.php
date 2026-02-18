<?php
require_once '../includes/db.php';
$cats = dbQuery("SELECT * FROM categories");
echo "Current Categories:\n";
foreach ($cats as $c) {
    echo "ID: " . $c['id'] . " - Name: " . $c['name'] . " - Slug: " . $c['slug'] . "\n";
}
?>
