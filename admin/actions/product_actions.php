<?php
/**
 * Product Actions - HYPE Sportswear CMS
 * Procesa crear, editar y eliminar productos
 */

session_start();
require_once '../../includes/db.php';
require_once '../../includes/image_helper.php';
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
            // Recoger y validar datos
            $name = strip_tags(trim($_POST['name'] ?? ''));
            $description = strip_tags(trim($_POST['description'] ?? ''));
            $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
            $category_id = filter_var($_POST['category_id'], FILTER_VALIDATE_INT);
            $stock = filter_var($_POST['stock'], FILTER_VALIDATE_INT);
            $sizes = strip_tags(trim($_POST['sizes'] ?? 'S,M,L'));
            $is_featured = isset($_POST['is_featured']) ? 1 : 0;
            $imageUrl = '';

            if (empty($name) || !$price || !$category_id || $stock === false) {
                throw new Exception("Datos inválidos. Revisa precios y stock.");
            }

            // Manejo de Imagen Seguro
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                // Verificar tipo MIME real
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->file($_FILES['image']['tmp_name']);
                
                $allowedMimes = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp',
                    'image/gif' => 'gif'
                ];

                if (!array_key_exists($mimeType, $allowedMimes)) {
                    throw new Exception("Formato de imagen inválido (MIME).");
                }

                // Generar nombre base seguro
                $fileNameBase = bin2hex(random_bytes(16));
                $targetFile = $targetDir . $fileNameBase . '.tmp'; // Helper le pondrá .webp

                try {
                    // Optimizar y convertir
                    $finalFileName = processImage(
                        $_FILES['image'], 
                        $targetFile
                    );
                    $imageUrl = $dbDir . $finalFileName;
                    
                    // Limpia archivo temporal si quedó (processImage usa GD desde memoria)
                } catch (Exception $e) {
                    throw new Exception("Error al procesar imagen: " . $e->getMessage());
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
            $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
            $name = strip_tags(trim($_POST['name'] ?? ''));
            $description = strip_tags(trim($_POST['description'] ?? ''));
            $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
            $category_id = filter_var($_POST['category_id'], FILTER_VALIDATE_INT);
            $stock = filter_var($_POST['stock'], FILTER_VALIDATE_INT);
            $sizes = strip_tags(trim($_POST['sizes'] ?? 'S,M,L'));
            $is_featured = isset($_POST['is_featured']) ? 1 : 0;
            
            if (!$id || empty($name) || !$price || !$category_id || $stock === false) {
                 throw new Exception("Datos inválidos en actualización.");
            }

            // Mantener imagen actual por defecto
            $imageUrl = $_POST['current_image'];

            // Si suben nueva imagen
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->file($_FILES['image']['tmp_name']);
                
                $allowedMimes = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp',
                    'image/gif' => 'gif'
                ];

                if (!array_key_exists($mimeType, $allowedMimes)) {
                    throw new Exception("Formato de imagen inválido (MIME).");
                }

                $fileNameBase = bin2hex(random_bytes(16));
                $targetFile = $targetDir . $fileNameBase . '.tmp';
                
                try {
                    $finalFileName = processImage(
                        $_FILES['image'], 
                        $targetFile
                    );
                    $imageUrl = $dbDir . $finalFileName;
                } catch (Exception $e) {
                     throw new Exception("Error al procesar nueva imagen: " . $e->getMessage());
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

            $result = dbExecute("DELETE FROM products WHERE id = ?", [$id]);
            
            if ($result === false) {
                // Probable error de restricción de clave foránea
                throw new Exception("No se pudo eliminar el producto. Verifica que no tenga pedidos asociados.");
            }
            
            if ($result === 0) {
                 throw new Exception("Producto no encontrado o ya eliminado.");
            }

            logAction('producto_eliminado', "Producto eliminado ID: $id");

            header('Location: ../products.php?success=deleted');
            exit;
        }

    } catch (Exception $e) {
        // Log error
        logAction('error_producto', "Error en gestión de productos: " . $e->getMessage());
        header('Location: ../products.php?error=' . urlencode($e->getMessage()));
        exit;
    }
}
