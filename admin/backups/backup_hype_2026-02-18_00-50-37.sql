-- HYPE CMS Database Backup
-- Fecha: 2026-02-18 00:50:37

SET FOREIGN_KEY_CHECKS=0;



CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO categories VALUES("1","Camisetas","2026-02-17 17:08:36");
INSERT INTO categories VALUES("2","Pantalones","2026-02-17 17:08:36");
INSERT INTO categories VALUES("3","Zapatos","2026-02-17 17:08:36");
INSERT INTO categories VALUES("4","Accesorios","2026-02-17 17:08:36");


CREATE TABLE `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_logs_user_id` (`user_id`),
  KEY `idx_logs_action` (`action`),
  KEY `idx_logs_created_at` (`created_at`),
  CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO logs VALUES("1","","login","Inicio de sesión exitoso: admin","127.0.0.1","2026-02-17 20:44:39");
INSERT INTO logs VALUES("2","","producto_editado","Producto actualizado ID: 3 (Zapatillas Speed Max de prueba)","127.0.0.1","2026-02-17 20:45:34");
INSERT INTO logs VALUES("3","","producto_creado","Producto creado: comida","127.0.0.1","2026-02-17 20:46:09");


CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `total_amount_bs` decimal(12,2) DEFAULT NULL,
  `status` enum('pending','processing','completed','cancelled') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_orders_status` (`status`),
  KEY `idx_orders_created_at` (`created_at`),
  KEY `idx_orders_payment_method` (`payment_method`),
  KEY `idx_orders_date_status` (`created_at`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `payment_methods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO payment_methods VALUES("1","Pago Móvil","Pago instantáneo interbancario","Banco: Banco de Venezuela\nTeléfono: 0414-1234567\nC.I: 12.345.678","1","2026-02-17 19:32:14","2026-02-17 19:32:14");
INSERT INTO payment_methods VALUES("2","Zelle","Transferencia en dólares","Email: pagos@hypesportswear.com\nTitular: Hype Sportswear LLC","1","2026-02-17 19:32:14","2026-02-17 19:32:14");
INSERT INTO payment_methods VALUES("3","Efectivo (Divisas)","Pago al momento de la entrega","Solo billetes en buen estado y monto exacto.","1","2026-02-17 19:32:14","2026-02-17 19:32:14");
INSERT INTO payment_methods VALUES("4","Transferencia Bancaria","Transferencia nacional","Banco: Banesco\nCuenta: 0134-xxxx-xxxx-xxxx\nTitular: Hype Sportswear","0","2026-02-17 19:32:14","2026-02-17 19:32:14");


CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category_id` int(11) NOT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category_id`),
  KEY `idx_featured` (`is_featured`),
  KEY `idx_created` (`created_at`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO products VALUES("1","Camiseta Deportiva Pro","Camiseta de alto rendimiento con tecnología dry-fit","29.99","1","images/products/camiseta-pro.jpg","50","1","2026-02-17 17:08:36","2026-02-17 17:08:36");
INSERT INTO products VALUES("2","Pantalón Running Elite","Pantalón ligero ideal para correr","45.99","2","images/products/pantalon-elite.jpg","30","1","2026-02-17 17:08:36","2026-02-17 17:08:36");
INSERT INTO products VALUES("3","Zapatillas Speed Max de prueba","Zapatillas profesionales para máximo rendimiento","50.00","3","images/products/zapatillas-speed.jpg","20","1","2026-02-17 17:08:36","2026-02-17 20:45:34");
INSERT INTO products VALUES("4","Gorra HYPE Original","Gorra deportiva con logo bordado","15.99","4","images/products/gorra-hype.jpg","100","0","2026-02-17 17:08:36","2026-02-17 17:08:36");
INSERT INTO products VALUES("5","comida","prueba","20.00","2","images/products/1771375569_Imagen de WhatsApp 2025-07-06 a las 17.19.56_c271a586 (1).jpg","5","1","2026-02-17 20:46:09","2026-02-17 20:46:09");


CREATE TABLE `rate_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rate` decimal(10,2) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_rate_history_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL,
  `value` text NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO settings VALUES("1","tasa_bcv","36.50","Tasa de cambio BCV (Bs/$) - Se actualiza manualmente si la API falla","2026-02-17 17:08:36");
INSERT INTO settings VALUES("2","banner_titulo","¡Bienvenido a HYPE Sportswear!","Título principal del banner","2026-02-17 17:08:36");
INSERT INTO settings VALUES("3","banner_subtitulo","Las mejores prendas deportivas al mejor precio","Subtítulo del banner","2026-02-17 17:08:36");
INSERT INTO settings VALUES("4","banner_texto_boton","Ver Productos","Texto del botón del banner","2026-02-17 17:08:36");


CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users VALUES("6","admin","$2y$12$KdWlxRi8a9yIJmeDrBl2ze/3RCaXelH.PzNaaRy5Ehq0W7mLqTcWa","2026-02-17 19:02:37","2026-02-17 19:08:53");


SET FOREIGN_KEY_CHECKS=1;