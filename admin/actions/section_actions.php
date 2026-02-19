<?php
/**
 * Section Actions - HYPE Sportswear CMS
 * Procesa la actualización de secciones
 */

session_start();
require_once '../../includes/db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $id = $_POST['section_id'] ?? null;
    $title = $_POST['title'] ?? '';
    $subtitle = $_POST['subtitle'] ?? '';
    $link_text = $_POST['link_text'] ?? '';
    $link_url = $_POST['link_url'] ?? '';
    $current_image = $_POST['current_image'] ?? '';

    if (!$id) {
        die("Error: ID de sección no proporcionado.");
    }

    // 1. Manejo de Imagen
    $image_url = $current_image;
    
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === 0) {
        $upload_dir = '../../img/uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_name = time() . '_' . basename($_FILES['image_file']['name']);
        $target_file = $upload_dir . $file_name;
        
        // Validar tipo de archivo
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($imageFileType, $allowed)) {
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $target_file)) {
                $image_url = 'img/uploads/' . $file_name;
            } else {
                error_log("Error al subir imagen: " . print_r($_FILES, true));
            }
        }
    }

    // 2. Actualizar BD
    try {
        $sql = "UPDATE sections SET 
                title = ?, 
                subtitle = ?, 
                image_url = ?, 
                link_text = ?, 
                link_url = ? 
                WHERE id = ?";
        
        dbExecute($sql, [$title, $subtitle, $image_url, $link_text, $link_url, $id]);

        header('Location: ../sections.php?success=1');
        exit;

    } catch (Exception $e) {
        die("Error al actualizar: " . $e->getMessage());
    }
}
