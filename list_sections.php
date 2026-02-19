<?php
require 'includes/db.php';
$rows = dbQuery("SELECT name FROM sections");
foreach ($rows as $r) {
    echo $r['name'] . "\n";
}
