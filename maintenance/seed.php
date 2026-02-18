<?php
/**
 * Script Seed - Crear Usuario Administrador
 * Ejecutar UNA SOLA VEZ para crear el usuario inicial
 * 
 * Uso: Acceder a http://localhost/seed.php desde el navegador
 */

require_once '../includes/db.php';

// Configuración del usuario administrador
$username = 'admin';
$password = 'admin123'; // Cambiar esta contraseña después del primer login

// Encriptar la contraseña usando bcrypt
$password_hash = password_hash($password, PASSWORD_BCRYPT);

try {
    // Verificar si ya existe un usuario admin
    $existing = dbQueryOne("SELECT id FROM users WHERE username = ?", [$username]);
    
    if ($existing) {
        // ACTUALIZAR el usuario existente
        $result = dbExecute(
            "UPDATE users SET password_hash = ? WHERE username = ?",
            [$password_hash, $username]
        );
        
        echo "✅ <strong>Contraseña actualizada exitosamente!</strong><br><br>";
        echo "El usuario '<strong>$username</strong>' ya existía, se han actualizado sus credenciales.<br>";
        echo "<strong>Nuevas Credenciales:</strong><br>";
        echo "Usuario: <code style='background:#f4f4f4; padding:4px 8px; border-radius:4px;'>$username</code><br>";
        echo "Contraseña: <code style='background:#f4f4f4; padding:4px 8px; border-radius:4px;'>$password</code><br><br>";
        echo "<a href='admin/login.php' style='display:inline-block; background:#D6FE00; color:#0F1011; padding:10px 20px; text-decoration:none; border-radius:4px; font-weight:bold;'>IR AL LOGIN →</a><br><br>";
        echo "<hr><br>";
    } else {
        // Insertar el usuario administrador
        $userId = dbExecute(
            "INSERT INTO users (username, password_hash) VALUES (?, ?)",
            [$username, $password_hash]
        );
        
        if ($userId) {
            echo "✅ <strong>Usuario administrador creado exitosamente!</strong><br><br>";
            echo "<strong>Credenciales:</strong><br>";
            echo "Usuario: <code style='background:#f4f4f4; padding:4px 8px; border-radius:4px;'>$username</code><br>";
            echo "Contraseña: <code style='background:#f4f4f4; padding:4px 8px; border-radius:4px;'>$password</code><br><br>";
            echo "<strong>⚠️ IMPORTANTE:</strong> Cambia la contraseña después del primer login.<br><br>";
            echo "<a href='admin/login.php' style='display:inline-block; background:#D6FE00; color:#0F1011; padding:10px 20px; text-decoration:none; border-radius:4px; font-weight:bold;'>IR AL LOGIN →</a><br><br>";
            echo "<hr><br>";
            echo "<small style='color:#666;'>Puedes eliminar este archivo (seed.php) por seguridad después de crear tu usuario.</small>";
        } else {
            echo "❌ Error al crear el usuario. Revisa la configuración de la base de datos.";
        }
    }
    
} catch (Exception $e) {
    echo "❌ <strong>Error:</strong> " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seed - Crear Usuario Admin</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            line-height: 1.6;
        }
        code {
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
</body>
</html>
