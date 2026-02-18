<?php
/**
 * Admin Login - HYPE Sportswear CMS
 * Sistema de autenticación seguro
 */

session_start();

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

require_once '../db.php';

$error_message = '';

// Procesar el formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error_message = 'Por favor completa todos los campos.';
    } else {
        // Buscar el usuario en la base de datos
        $user = dbQueryOne(
            "SELECT id, username, password_hash FROM users WHERE username = ?",
            [$username]
        );
        
        if ($user && password_verify($password, $user['password_hash'])) {
            // Login exitoso
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            
            // Regenerar ID de sesión por seguridad
            session_regenerate_id(true);

            // LOG
            require_once 'includes/logger.php';
            logAction('login', 'Inicio de sesión exitoso: ' . $user['username']);
            
            // Redirigir al dashboard
            header('Location: dashboard.php');
            exit;
        } else {
            $error_message = 'Usuario o contraseña incorrectos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - HYPE CMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #0F1011;
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            background: #1A1B1E;
            border: 1px solid #2A2B2E;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
        }

        .logo {
            font-family: 'Chakra Petch', sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 8px;
        }

        .logo .neon {
            color: #D6FE00;
        }

        .subtitle {
            text-align: center;
            color: #888;
            font-size: 0.9rem;
            margin-bottom: 32px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            color: #D6FE00;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 1.2rem;
        }

        input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            background: #0F1011;
            border: 2px solid #2A2B2E;
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        input:focus {
            outline: none;
            border-color: #D6FE00;
            box-shadow: 0 0 0 3px rgba(214, 254, 0, 0.1);
        }

        input::placeholder {
            color: #555;
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background: #D6FE00;
            color: #0F1011;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Chakra Petch', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-login:hover {
            background: #c4ec00;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(214, 254, 0, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .error-message {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #ff6b6b;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 24px;
            color: #888;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: #D6FE00;
        }

        .footer {
            text-align: center;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #2A2B2E;
            color: #666;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            HYPE<span class="neon">.</span>
        </div>
        <div class="subtitle">Panel de Administración</div>

        <?php if ($error_message): ?>
            <div class="error-message">
                <i class="ph ph-warning-circle"></i>
                <span><?php echo htmlspecialchars($error_message); ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Usuario</label>
                <div class="input-wrapper">
                    <i class="ph ph-user"></i>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        placeholder="Ingresa tu usuario"
                        required
                        autocomplete="username"
                        autofocus
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <div class="input-wrapper">
                    <i class="ph ph-lock"></i>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Ingresa tu contraseña"
                        required
                        autocomplete="current-password"
                    >
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="ph ph-sign-in"></i>
                Ingresar al CMS
            </button>
        </form>

        <a href="../index.php" class="back-link">
            ← Volver al sitio
        </a>

        <div class="footer">
            © 2026 HYPE Sportswear. Sistema Seguro.
        </div>
    </div>
</body>
</html>
