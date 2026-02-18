-- ============================================
-- Actualización de Schema: CRUD Support
-- ============================================

-- Tabla: payment_methods
CREATE TABLE IF NOT EXISTS payment_methods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    instructions TEXT, -- Instrucciones para el usuario (ej: datos de pago móvil)
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos iniciales para métodos de pago
INSERT INTO payment_methods (name, description, instructions, is_active) VALUES 
('Pago Móvil', 'Pago instantáneo interbancario', 'Banco: Banco de Venezuela\nTeléfono: 0414-1234567\nC.I: 12.345.678', TRUE),
('Zelle', 'Transferencia en dólares', 'Email: pagos@hypesportswear.com\nTitular: Hype Sportswear LLC', TRUE),
('Efectivo (Divisas)', 'Pago al momento de la entrega', 'Solo billetes en buen estado y monto exacto.', TRUE),
('Transferencia Bancaria', 'Transferencia nacional', 'Banco: Banesco\nCuenta: 0134-xxxx-xxxx-xxxx\nTitular: Hype Sportswear', FALSE);
