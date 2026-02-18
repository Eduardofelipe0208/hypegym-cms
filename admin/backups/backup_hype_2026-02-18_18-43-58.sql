-- HYPE CMS Database Backup
-- Fecha: 2026-02-18 18:43:58

SET FOREIGN_KEY_CHECKS=0;



CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO categories VALUES("1","Camisetas","2026-02-17 17:08:36");
INSERT INTO categories VALUES("2","Pantalones","2026-02-17 17:08:36");
INSERT INTO categories VALUES("3","Zapatos","2026-02-17 17:08:36");
INSERT INTO categories VALUES("4","Accesorios","2026-02-17 17:08:36");
INSERT INTO categories VALUES("5","GYM WEAR","2026-02-18 13:35:21");
INSERT INTO categories VALUES("6","STREET","2026-02-18 13:35:21");


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
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO logs VALUES("1","","login","Inicio de sesión exitoso: admin","127.0.0.1","2026-02-17 20:44:39");
INSERT INTO logs VALUES("2","","producto_editado","Producto actualizado ID: 3 (Zapatillas Speed Max de prueba)","127.0.0.1","2026-02-17 20:45:34");
INSERT INTO logs VALUES("3","","producto_creado","Producto creado: comida","127.0.0.1","2026-02-17 20:46:09");
INSERT INTO logs VALUES("4","","backup_creado","Backup generado: backup_hype_2026-02-18_00-50-37.sql","127.0.0.1","2026-02-17 20:50:37");
INSERT INTO logs VALUES("5","","config_actualizada","Se actualizaron las configuraciones del sistema","127.0.0.1","2026-02-17 20:51:33");
INSERT INTO logs VALUES("6","","config_actualizada","Se actualizaron las configuraciones del sistema","127.0.0.1","2026-02-17 20:51:42");
INSERT INTO logs VALUES("7","","config_actualizada","Se actualizaron las configuraciones del sistema","127.0.0.1","2026-02-17 20:59:14");
INSERT INTO logs VALUES("8","","config_actualizada","Se actualizaron las configuraciones del sistema","127.0.0.1","2026-02-17 21:02:54");
INSERT INTO logs VALUES("9","","producto_eliminado","Producto eliminado ID: 3","127.0.0.1","2026-02-17 21:03:07");
INSERT INTO logs VALUES("10","","producto_creado","Producto creado: pizza","127.0.0.1","2026-02-17 21:16:39");
INSERT INTO logs VALUES("11","","config_actualizada","Se actualizaron las configuraciones del sistema","127.0.0.1","2026-02-17 21:17:21");
INSERT INTO logs VALUES("12","","login","Inicio de sesión exitoso: admin","127.0.0.1","2026-02-18 11:10:04");
INSERT INTO logs VALUES("13","","config_actualizada","Se actualizaron las configuraciones del sistema","127.0.0.1","2026-02-18 11:10:57");
INSERT INTO logs VALUES("14","","producto_editado","Producto actualizado ID: 4 (Gorra HYPE Original)","127.0.0.1","2026-02-18 11:13:47");
INSERT INTO logs VALUES("15","","producto_editado","Producto actualizado ID: 1 (Camiseta Deportiva Pro)","127.0.0.1","2026-02-18 11:13:58");
INSERT INTO logs VALUES("16","","producto_editado","Producto actualizado ID: 2 (Pantalón Running Elite)","127.0.0.1","2026-02-18 11:14:17");
INSERT INTO logs VALUES("17","","producto_creado","Producto creado: prueba","127.0.0.1","2026-02-18 11:16:20");
INSERT INTO logs VALUES("18","","producto_editado","Producto actualizado ID: 7 (prueba)","127.0.0.1","2026-02-18 11:16:49");
INSERT INTO logs VALUES("19","","login","Inicio de sesión exitoso: admin","127.0.0.1","2026-02-18 11:34:10");
INSERT INTO logs VALUES("20","","config_actualizada","Se actualizaron las configuraciones del sistema","127.0.0.1","2026-02-18 11:42:16");
INSERT INTO logs VALUES("21","","config_actualizada","Se actualizaron las configuraciones del sistema","127.0.0.1","2026-02-18 12:05:32");
INSERT INTO logs VALUES("22","","producto_editado","Producto actualizado ID: 7 (prueba)","127.0.0.1","2026-02-18 12:06:14");
INSERT INTO logs VALUES("23","","producto_editado","Producto actualizado ID: 7 (prueba)","127.0.0.1","2026-02-18 13:35:55");
INSERT INTO logs VALUES("24","","producto_editado","Producto actualizado ID: 6 (pizza)","127.0.0.1","2026-02-18 13:37:23");
INSERT INTO logs VALUES("25","","producto_editado","Producto actualizado ID: 1 (Camiseta Deportiva Pro)","127.0.0.1","2026-02-18 13:37:39");
INSERT INTO logs VALUES("26","","producto_editado","Producto actualizado ID: 2 (Pantalón Running Elite)","127.0.0.1","2026-02-18 13:37:48");
INSERT INTO logs VALUES("27","","producto_editado","Producto actualizado ID: 4 (Gorra HYPE Original)","127.0.0.1","2026-02-18 13:37:57");
INSERT INTO logs VALUES("28","","config_actualizada","Se actualizaron las configuraciones del sistema","127.0.0.1","2026-02-18 13:47:04");
INSERT INTO logs VALUES("29","","config_actualizada","Se actualizaron las configuraciones del sistema","127.0.0.1","2026-02-18 13:47:29");
INSERT INTO logs VALUES("30","","config_actualizada","Se actualizaron las configuraciones del sistema","127.0.0.1","2026-02-18 13:48:27");
INSERT INTO logs VALUES("31","","config_actualizada","Se actualizaron las configuraciones del sistema","127.0.0.1","2026-02-18 14:01:46");
INSERT INTO logs VALUES("32","","config_actualizada","Se actualizaron las configuraciones del sistema","127.0.0.1","2026-02-18 14:02:26");
INSERT INTO logs VALUES("33","","config_actualizada","Se actualizaron las configuraciones del sistema","127.0.0.1","2026-02-18 14:04:20");


CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO order_items VALUES("1","1","1","2","29.99");
INSERT INTO order_items VALUES("2","2","2","1","45.99");
INSERT INTO order_items VALUES("3","3","5","1","20.00");


CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `customer_address` text DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `total_amount_bs` decimal(12,2) DEFAULT NULL,
  `exchange_rate` decimal(10,2) DEFAULT 0.00,
  `status` enum('pending','processing','completed','cancelled') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_reference` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_orders_status` (`status`),
  KEY `idx_orders_created_at` (`created_at`),
  KEY `idx_orders_payment_method` (`payment_method`),
  KEY `idx_orders_date_status` (`created_at`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO orders VALUES("1","q","","0425861","222","59.98","26991.00","450.00","pending","Pago Móvil","123","","2026-02-17 21:08:58","2026-02-17 21:08:58");
INSERT INTO orders VALUES("2","sss","","q","123","45.99","21155.40","460.00","pending","Efectivo (Divisas)","123","","2026-02-17 21:17:52","2026-02-17 21:17:52");
INSERT INTO orders VALUES("3","Eduardo Marcano","eduardofelipe020800@gmail.com","s","asd","20.00","9200.00","460.00","pending","Pago Móvil","123","","2026-02-18 11:07:45","2026-02-18 11:07:45");


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
  `sizes` varchar(255) DEFAULT 'S,M,L',
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO products VALUES("1","Camiseta Deportiva Pro","Camiseta de alto rendimiento con tecnología dry-fit","29.99","S,M,L","6","images/products/1771427638_7.jpg","48","1","2026-02-17 17:08:36","2026-02-18 13:37:39");
INSERT INTO products VALUES("2","Pantalón Running Elite","Pantalón ligero ideal para correr","45.99","S,M,L","5","images/products/1771427657_6.jpg","29","1","2026-02-17 17:08:36","2026-02-18 13:37:48");
INSERT INTO products VALUES("4","Gorra HYPE Original","Gorra deportiva con logo bordado","15.99","S,M,L","4","images/products/1771427627_1.webp","100","0","2026-02-17 17:08:36","2026-02-18 13:37:57");
INSERT INTO products VALUES("5","comida","prueba","20.00","S,M,L","1","images/products/1771375569_Imagen de WhatsApp 2025-07-06 a las 17.19.56_c271a586 (1).jpg","4","1","2026-02-17 20:46:09","2026-02-18 13:36:09");
INSERT INTO products VALUES("6","pizza","holi","40.00","S,M,L","4","","30","1","2026-02-17 21:16:39","2026-02-18 13:37:23");
INSERT INTO products VALUES("7","prueba","1234","5.00","S,M,L","1","images/products/1771427809_2.webp","3","1","2026-02-18 11:16:20","2026-02-18 13:36:09");


CREATE TABLE `rate_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rate` decimal(10,2) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_rate_history_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO rate_history VALUES("1","450.00","1","2026-02-17 20:51:33");
INSERT INTO rate_history VALUES("2","460.00","1","2026-02-17 21:17:21");


CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL,
  `value` text NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO settings VALUES("1","tasa_bcv","460","Tasa de cambio BCV (Bs/$) - Se actualiza manualmente si la API falla","2026-02-17 21:17:21");
INSERT INTO settings VALUES("2","banner_titulo","¡Bienvenido a HYPE Sportswear!","Título principal del banner","2026-02-17 17:08:36");
INSERT INTO settings VALUES("3","banner_subtitulo","Las mejores prendas deportivas al mejor precio","Subtítulo del banner","2026-02-17 17:08:36");
INSERT INTO settings VALUES("4","banner_texto_boton","Ver Productos","Texto del botón del banner","2026-02-17 17:08:36");
INSERT INTO settings VALUES("5","site_logo_text","kano","","2026-02-18 12:05:32");
INSERT INTO settings VALUES("6","primary_color","#00ff59","","2026-02-18 14:04:20");
INSERT INTO settings VALUES("7","hero_title","prueba","","2026-02-18 11:10:57");
INSERT INTO settings VALUES("8","hero_subtitle","ccca","","2026-02-18 12:05:32");
INSERT INTO settings VALUES("9","whatsapp_number","04120936783","","2026-02-17 21:17:21");
INSERT INTO settings VALUES("29","hero_image","img/uploads/hero_1771436907_4.jpg","","2026-02-18 13:48:27");
INSERT INTO settings VALUES("30","social_instagram","","","2026-02-18 13:47:04");
INSERT INTO settings VALUES("31","social_tiktok","","","2026-02-18 13:47:04");


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