<?php
require 'includes/db.php';

$sections = [
    // Collections
    [
        'name' => 'collection_gym',
        'title' => 'GYM WEAR',
        'subtitle' => 'Ropa para entrenamiento intenso',
        'image_url' => 'https://images.unsplash.com/photo-1581009146145-b5ef050c2e1e?q=80&w=1470&auto=format&fit=crop',
        'link_text' => 'VER GYM',
        'link_url' => 'index.php?page=shop&category=gym%20wear'
    ],
    [
        'name' => 'collection_street',
        'title' => 'STREETWEAR',
        'subtitle' => 'Estilo urbano y comodidad',
        'image_url' => 'img/4.jpg',
        'link_text' => 'VER STREET',
        'link_url' => 'index.php?page=shop&category=street'
    ],
    [
        'name' => 'collection_accessories',
        'title' => 'ACCESORIOS',
        'subtitle' => 'Complementa tu outfit',
        'image_url' => 'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?q=80&w=1470&auto=format&fit=crop',
        'link_text' => 'VER ACCESORIOS',
        'link_url' => 'index.php?page=shop&category=accesorios'
    ],
    // Benefits
    [
        'name' => 'benefit_energy',
        'title' => 'ENERGÍA',
        'subtitle' => 'Diseños para romper tus límites.',
        'image_url' => '', // Icon managed by code/class, or maybe use image field for icon class/url?
        'link_text' => 'ph-lightning', // Storing icon usage in link_text for now or just hardcode structure
        'link_url' => ''
    ],
    [
        'name' => 'benefit_quality',
        'title' => 'CALIDAD',
        'subtitle' => 'Telas premium anti-transpirantes.',
        'link_text' => 'ph-medal',
        'link_url' => ''
    ],
    [
        'name' => 'benefit_shipping',
        'title' => 'ENVÍOS',
        'subtitle' => 'Rápidos y seguros a todo el país.',
        'link_text' => 'ph-truck',
        'link_url' => ''
    ],
    // Bestsellers Title
    [
        'name' => 'home_bestsellers',
        'title' => 'LO MÁS VENDIDO',
        'subtitle' => 'Nuestros productos estrella',
        'image_url' => '',
        'link_text' => 'VER TODO EL CATÁLOGO',
        'link_url' => 'index.php?page=shop'
    ]
];

$sql = "INSERT INTO sections (name, title, subtitle, image_url, link_text, link_url) VALUES (?, ?, ?, ?, ?, ?)";

foreach ($sections as $s) {
    // Check if exists
    $exists = dbQueryOne("SELECT id FROM sections WHERE name = ?", [$s['name']]);
    if (!$exists) {
        dbExecute($sql, [
            $s['name'], 
            $s['title'], 
            $s['subtitle'], 
            $s['image_url'] ?? '', 
            $s['link_text'] ?? '', 
            $s['link_url'] ?? ''
        ]);
        echo "Inserted: " . $s['name'] . "\n";
    } else {
        echo "Skipped: " . $s['name'] . " (Exists)\n";
    }
}
