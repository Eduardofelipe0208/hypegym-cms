<?php
require_once '../includes/db.php';
$count = dbQueryOne("SELECT COUNT(*) as c FROM products");
echo "Products: " . $count['c'];
?>
