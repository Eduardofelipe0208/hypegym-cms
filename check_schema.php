<?php
require 'includes/db.php';
$cols = dbQuery("DESCRIBE order_items");
foreach($cols as $c) {
    echo $c['Field'] . "\n";
}
