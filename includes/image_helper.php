<?php
/**
 * Image Helper - HYPE Sportswear CMS
 * Optimiza, redimensiona y convierte imágenes a WebP
 */

function processImage($sourceMeta, $destinationPath, $maxWidth = 1920, $quality = 80) {
    if (!extension_loaded('gd')) {
        throw new Exception("La extensión GD no está habilitada.");
    }

    $sourcePath = $sourceMeta['tmp_name'];
    $mimeType = $sourceMeta['type'] ?? '';
    
    // Validar si es imagen real
    $info = getimagesize($sourcePath);
    if ($info === false) {
        throw new Exception("El archivo no es una imagen válida.");
    }

    $mime = $info['mime'];
    $srcImage = null;

    // Crear recurso de imagen según tipo
    switch ($mime) {
        case 'image/jpeg':
            $srcImage = imagecreatefromjpeg($sourcePath);
            break;
        case 'image/png':
            $srcImage = imagecreatefrompng($sourcePath);
            break;
        case 'image/webp':
            $srcImage = imagecreatefromwebp($sourcePath);
            break;
        case 'image/gif':
            $srcImage = imagecreatefromgif($sourcePath);
            break;
        default:
            throw new Exception("Formato no soportado: $mime");
    }

    if (!$srcImage) {
        throw new Exception("Error al procesar la imagen.");
    }

    // Calcular nuevas dimensiones
    $width = imagesx($srcImage);
    $height = imagesy($srcImage);
    
    if ($width > $maxWidth) {
        $newWidth = $maxWidth;
        $newHeight = intval($height * ($maxWidth / $width));
        
        $dstImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Mantener transparencia para PNG/WebP
        if ($mime == 'image/png' || $mime == 'image/webp') {
            imagealphablending($dstImage, false);
            imagesavealpha($dstImage, true);
            $transparent = imagecolorallocatealpha($dstImage, 255, 255, 255, 127);
            imagefilledrectangle($dstImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($srcImage);
        $srcImage = $dstImage;
    }

    // Convertir ruta destino a .webp
    $pathInfo = pathinfo($destinationPath);
    $finalPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';

    // Guardar como WebP
    if (!imagewebp($srcImage, $finalPath, $quality)) {
        throw new Exception("Error al guardar la imagen WebP.");
    }

    // Liberar memoria
    imagedestroy($srcImage);

    // Devolver nueva ruta relativa (asumiendo estructura de proyecto)
    // El destinationPath que llega suele ser absoluto o relativo a actions/
    // Devolvemos el nombre del archivo generado para que el caller decida la ruta BD
    return basename($finalPath);
}
