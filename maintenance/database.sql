-- ============================================
-- Base de Datos: hype_shop
-- E-commerce HYPE Sportswear
-- ============================================

CREATE DATABASE IF NOT EXISTS hype_shop
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE hype_shop;

-- ============================================
-- Tabla: users
-- Almacena los usuarios administradores
-- ============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabla: categories
-- Almacena las categorías de productos
-- ============================================
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabla: products
-- Almacena los productos del e-commerce
-- ============================================
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category_id INT NOT NULL,
    image_url VARCHAR(500),
    stock INT NOT NULL DEFAULT 0,
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    INDEX idx_category (category_id),
    INDEX idx_featured (is_featured),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabla: settings
-- Almacena configuraciones generales del sitio
-- ============================================
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(100) NOT NULL UNIQUE,
    `value` TEXT NOT NULL,
    description VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Datos iniciales
-- ============================================

-- Insertar usuario administrador por defecto
-- Usuario: admin / Contraseña: admin123 (CAMBIAR EN PRODUCCIÓN)
INSERT INTO users (username, password_hash) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insertar categorías de ejemplo
INSERT INTO categories (name) VALUES 
('Camisetas'),
('Pantalones'),
('Zapatos'),
('Accesorios');

-- Insertar configuraciones por defecto
INSERT INTO settings (`key`, `value`, description) VALUES 
('tasa_bcv', '36.50', 'Tasa de cambio BCV (Bs/$) - Se actualiza manualmente si la API falla'),
('banner_titulo', '¡Bienvenido a HYPE Sportswear!', 'Título principal del banner'),
('banner_subtitulo', 'Las mejores prendas deportivas al mejor precio', 'Subtítulo del banner'),
('banner_texto_boton', 'Ver Productos', 'Texto del botón del banner');

-- Insertar productos de ejemplo
INSERT INTO products (name, description, price, category_id, image_url, stock, is_featured) VALUES 
('Camiseta Deportiva Pro', 'Camiseta de alto rendimiento con tecnología dry-fit', 29.99, 1, 'images/products/camiseta-pro.jpg', 50, TRUE),
('Pantalón Running Elite', 'Pantalón ligero ideal para correr', 45.99, 2, 'images/products/pantalon-elite.jpg', 30, TRUE),
('Zapatillas Speed Max', 'Zapatillas profesionales para máximo rendimiento', 89.99, 3, 'images/products/zapatillas-speed.jpg', 20, TRUE),
('Gorra HYPE Original', 'Gorra deportiva con logo bordado', 15.99, 4, 'images/products/gorra-hype.jpg', 100, FALSE);
