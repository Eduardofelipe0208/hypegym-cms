-- ============================================
-- Actualización de Schema: Historial de Tasas
-- ============================================

-- 1. Tabla Historial de Tasas
CREATE TABLE IF NOT EXISTS rate_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rate DECIMAL(10, 2) NOT NULL,
    user_id INT, -- Opcional, si queremos trackear quién (NULL si es sistema o no hay sesión)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Columna Snapshot en Pedidos
-- Almacena el monto final en Bolívares al momento de la compra (Inmutable)
ALTER TABLE orders 
ADD COLUMN total_amount_bs DECIMAL(12, 2) AFTER total_amount;

-- 3. Índices
CREATE INDEX idx_rate_history_created_at ON rate_history(created_at);
