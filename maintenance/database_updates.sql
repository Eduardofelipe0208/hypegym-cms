-- ============================================
-- Actualización de Schema: Orders y Order Items
-- ============================================

-- Tabla: orders
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20),
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    payment_method VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: order_items
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Datos de prueba para el Dashboard (SEED)
-- ============================================

-- Limpiar datos anteriores de prueba (opcional, comentar si se quiere mantener)
-- SET FOREIGN_KEY_CHECKS = 0;
-- TRUNCATE TABLE order_items;
-- TRUNCATE TABLE orders;
-- SET FOREIGN_KEY_CHECKS = 1;

-- Pedidos recientes (Últimos 7 días)
INSERT INTO orders (customer_name, customer_email, total_amount, status, created_at) VALUES
('Juan Pérez', 'juan@example.com', 59.98, 'completed', DATE_SUB(NOW(), INTERVAL 1 DAY)),
('Maria Lopez', 'maria@example.com', 125.50, 'completed', DATE_SUB(NOW(), INTERVAL 2 DAY)),
('Carlos Ruiz', 'carlos@example.com', 29.99, 'pending', DATE_SUB(NOW(), INTERVAL 3 DAY)),
('Ana Silva', 'ana@example.com', 89.99, 'processing', DATE_SUB(NOW(), INTERVAL 5 DAY)),
('Pedro Diaz', 'pedro@example.com', 45.99, 'completed', DATE_SUB(NOW(), INTERVAL 6 DAY)),
('Lucia Mendez', 'lucia@example.com', 150.00, 'completed', NOW()),
('Roberto Gomez', 'roberto@example.com', 29.99, 'cancelled', DATE_SUB(NOW(), INTERVAL 1 DAY));

-- Pedidos de meses anteriores (Para gráfica mensual)
INSERT INTO orders (customer_name, customer_email, total_amount, status, created_at) VALUES
('Cliente Enero', 'enero@test.com', 1200.00, 'completed', '2026-01-15 10:00:00'),
('Cliente Diciembre', 'dic@test.com', 950.50, 'completed', '2025-12-20 14:30:00'),
('Cliente Noviembre', 'nov@test.com', 800.25, 'completed', '2025-11-05 09:15:00');

-- Items de pedidos (Para mejor vendidos)
-- Asumiendo IDs de productos 1, 2, 3, 4 existen del seed anterior
INSERT INTO order_items (order_id, product_id, quantity, price) 
SELECT id, 1, 2, 29.99 FROM orders WHERE customer_name = 'Juan Pérez'; -- Camisetas

INSERT INTO order_items (order_id, product_id, quantity, price)
SELECT id, 3, 1, 89.99 FROM orders WHERE customer_name = 'Maria Lopez'; -- Zapatillas

INSERT INTO order_items (order_id, product_id, quantity, price)
SELECT id, 2, 1, 45.99 FROM orders WHERE customer_name = 'Pedro Diaz'; -- Pantalón

INSERT INTO order_items (order_id, product_id, quantity, price)
SELECT id, 4, 3, 15.99 FROM orders WHERE customer_name = 'Lucia Mendez'; -- Gorras
