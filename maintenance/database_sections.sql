-- ============================================
-- Tabla: sections
-- Almacena contenido dinámico (Hero, Banners, etc.)
-- ============================================

CREATE TABLE IF NOT EXISTS sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE, -- Identificador único (ej: 'home_hero')
    title TEXT,
    subtitle TEXT,
    text_content TEXT,
    image_url VARCHAR(500),
    link_text VARCHAR(100),
    link_url VARCHAR(500),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Datos Iniciales (Seed)
-- ============================================

-- Hero Home
INSERT INTO sections (name, title, subtitle, link_text, link_url, image_url) VALUES 
('home_hero', 
 'SPORTSWEAR PARA LOS QUE <span class="text-neon">NO SE DETIENEN</span>', 
 'Diseño urbano, rendimiento profesional. HYPE no es moda, es mentalidad.', 
 'VER TIENDA', 
 'index.php?page=shop', 
 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?q=80&w=1470&auto=format&fit=crop'
) ON DUPLICATE KEY UPDATE id=id;

-- Banner Promo 1 (Ejemplo)
INSERT INTO sections (name, title, subtitle, link_text, link_url, image_url) VALUES 
('home_banner_1', 
 'NUEVA COLECCIÓN 2026', 
 'Descubre lo último en tecnología deportiva.', 
 'VER COLECCIÓN', 
 'index.php?page=shop&category=new', 
 'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?q=80&w=1470&auto=format&fit=crop'
) ON DUPLICATE KEY UPDATE id=id;
