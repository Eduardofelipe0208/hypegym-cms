-- ============================================
-- Actualización de Schema: Checkout & CMS
-- ============================================

-- 1. Actualizar tabla 'orders' para soportar pagos
ALTER TABLE orders 
ADD COLUMN payment_reference VARCHAR(100) AFTER total_amount,
ADD COLUMN exchange_rate DECIMAL(10, 2) AFTER payment_reference,
ADD COLUMN payment_method VARCHAR(50) AFTER exchange_rate,
ADD COLUMN notes TEXT AFTER status,
ADD COLUMN customer_phone VARCHAR(20) AFTER customer_email,
ADD COLUMN customer_address TEXT AFTER customer_phone;

-- 2. Tabla de Testimonios (CMS)
CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    author_name VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    rating INT DEFAULT 5,
    is_visible BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Configuraciones del CMS (Nuevos Keys)
-- Insertamos config por defecto si no existen (usando INSERT IGNORE)
INSERT IGNORE INTO settings (`key`, `value`, description) VALUES 
('site_logo_text', 'HYPE', 'Texto del Logo'),
('primary_color', '#D6FE00', 'Color Principal (Neon/Accent)'),
('hero_title', 'SPORTSWEAR PARA LOS QUE <span class="text-neon">NO SE DETIENEN</span>', 'Título del Banner Principal'),
('hero_subtitle', 'Diseño urbano, rendimiento profesional. HYPE no es moda, es mentalidad.', 'Subtítulo del Banner'),
('hero_image', 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?q=80&w=1470&auto=format&fit=crop', 'URL Imagen de Fondo Banner'),
('whatsapp_number', '584120936783', 'Número de WhatsApp para pedidos'),
('social_instagram', '#', 'Link Instagram'),
('social_tiktok', '#', 'Link TikTok');

-- Insertar testimonios de prueba
INSERT INTO testimonials (author_name, content, rating) VALUES 
('Carlos R.', 'La calidad de la tela es increíble, perfecto para entrenar pesado.', 5),
('Andrea M.', 'Me encantó el diseño del hoodie, se ve genial y es muy cómodo.', 5),
('Team Hype', 'Excelente servicio y entrega rápida en Caracas.', 5);
