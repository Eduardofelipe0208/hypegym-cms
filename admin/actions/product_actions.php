<?php
/**
 * Product Actions - HYPE Sportswear CMS
 * Procesa crear, editar y eliminar productos
 */

session_start();
require_once '../../includes/db.php';
require_once '../includes/logger.php';

// Validar seguridad
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['action'])) {
    
    $action = $_REQUEST['action'] ?? '';

    // Rutas de imágenes
    $targetDir = "../../images/products/";
    $dbDir = "images/products/"; // Ruta relativa para guardar en BD

    // Crear carpeta si no existe
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    try {
        if ($action === 'create') {
            // Recoger datos
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $category_id = $_POST['category_id'];
            $stock = $_POST['stock'];
            $sizes = $_POST['sizes'] ?? 'S,M,L'; // Default sizes
            $is_featured = isset($_POST['is_featured']) ? 1 : 0;
            $imageUrl = '';

            // Manejo de Imagen
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $fileName = time() . '_' . basename($_FILES['image']['name']);
                $targetFile = $targetDir . $fileName;
                
                // Validar extensión
                $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                
                if (in_array($imageFileType, $allowed)) {
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                        $imageUrl = $dbDir . $fileName;
                    } else {
                        throw new Exception("Error al mover la imagen subida.");
                    }
                } else {
                    throw new Exception("Formato de imagen no permitido.");
                }
            }

            // Insertar en BD
            $sql = "INSERT INTO products (name, description, price, category_id, stock, sizes, is_featured, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $result = dbExecute($sql, [$name, $description, $price, $category_id, $stock, $sizes, $is_featured, $imageUrl]);

            if (!$result) {
                throw new Exception("Error al insertar en base de datos.");
            }

            logAction('producto_creado', "Producto creado: $name");

            header('Location: ../products.php?success=created');
            exit;

        } elseif ($action === 'update') {
            $id = $_POST['id'];
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $category_id = $_POST['category_id'];
            $stock = $_POST['stock'];
            $sizes = $_POST['sizes'] ?? 'S,M,L';
            $is_featured = isset($_POST['is_featured']) ? 1 : 0;
            
            // Mantener imagen actual por defecto
            $imageUrl = $_POST['current_image'];

            // Si suben nueva imagen
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $fileName = time() . '_' . basename($_FILES['image']['name']);
                $targetFile = $targetDir . $fileName;
                
                $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                
                if (in_array($imageFileType, $allowed)) {
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                        $imageUrl = $dbDir . $fileName; // Actualizar con nueva ruta
                    } else {
                        throw new Exception("Error al mover la nueva imagen.");
                    }
                } else {
                    throw new Exception("Formato de imagen no permitido.");
                }
            }

            // Actualizar en BD
            $sql = "UPDATE products SET name=?, description=?, price=?, category_id=?, stock=?, sizes=?, is_featured=?, image_url=? WHERE id=?";
            $result = dbExecute($sql, [$name, $description, $price, $category_id, $stock, $sizes, $is_featured, $imageUrl, $id]);

            if ($result === false) {
                 throw new Exception("Error al actualizar en base de datos.");
            }

            logAction('producto_editado', "Producto actualizado ID: $id ($name)");

            header('Location: ../products.php?success=updated');
            exit;

        } elseif ($action === 'delete') {
            $id = $_GET['id'];
            
            // Opcional: Eliminar archivo de imagen físico si se desea limpiar
            // $prod = dbQueryOne("SELECT image_url FROM products WHERE id = ?", [$id]);
            // if ($prod && file_exists("../../" . $prod['image_url'])) unlink("../../" . $prod['image_url']);

            dbExecute("DELETE FROM products WHERE id = ?", [$id]);
            
            logAction('producto_eliminado', "Producto eliminado ID: $id");

            header('Location: ../products.php?success=deleted');
            exit;
        }

    } catch (Exception $e) {
        // Log error
        file_put_contents('error_log.txt', date('Y-m-d H:i:s') . " - Product Error: " . $e->getMessage() . "\n", FILE_APPEND);
        header('Location: ../products.php?error=' . urlencode($e->getMessage()));
        exit;
    }
}
