-- ============================================
-- Optimización: Índices para Filtros de Pedidos
-- ============================================

-- Índices para búsquedas rápidas por estado, fecha y método de pago
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_orders_created_at ON orders(created_at);
CREATE INDEX idx_orders_payment_method ON orders(payment_method);

-- Índice compuesto para filtros comunes (Fecha + Estado)
CREATE INDEX idx_orders_date_status ON orders(created_at, status);
