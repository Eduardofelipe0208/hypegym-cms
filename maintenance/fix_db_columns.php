<?php
require_once '../includes/db.php';

try {
    $db = getDB();
    echo "Conectado a la BD.<br>";
    
    // 1. Verificar columnas en orders
    $columns = $db->query("SHOW COLUMNS FROM orders")->fetchAll(PDO::FETCH_COLUMN);
    echo "Columnas actuales en orders: " . implode(", ", $columns) . "<br><br>";

    $updates = [];

    if (!in_array('total_amount_bs', $columns)) {
        $updates[] = "ADD COLUMN total_amount_bs DECIMAL(10,2) DEFAULT 0.00 AFTER total_amount";
    }
    if (!in_array('exchange_rate', $columns)) {
        $updates[] = "ADD COLUMN exchange_rate DECIMAL(10,2) DEFAULT 0.00 AFTER total_amount_bs";
    }
    if (!in_array('payment_method', $columns)) {
        $updates[] = "ADD COLUMN payment_method VARCHAR(50) DEFAULT NULL AFTER status";
    }
    if (!in_array('payment_reference', $columns)) {
        $updates[] = "ADD COLUMN payment_reference VARCHAR(100) DEFAULT NULL AFTER payment_method";
    }
    if (!in_array('customer_address', $columns)) {
        $updates[] = "ADD COLUMN customer_address TEXT AFTER customer_phone";
    }
    if (!in_array('notes', $columns)) {
        $updates[] = "ADD COLUMN notes TEXT AFTER payment_reference";
    }

    if (!in_array('notes', $columns)) {
        $updates[] = "ADD COLUMN notes TEXT AFTER payment_reference";
    }

    if (!empty($updates)) {
        foreach ($updates as $up) {
            $sql = "ALTER TABLE orders $up";
            echo "Ejecutando: $sql ... ";
            $db->exec($sql);
            echo "OK<br>";
        }
    } else {
        echo "La tabla 'orders' ya tiene todas las columnas necesarias.<br>";
    }

    // 1.5 Verificar columna sizes en products
    $prodColumns = $db->query("SHOW COLUMNS FROM products")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('sizes', $prodColumns)) {
        echo "Agregando columna 'sizes' a tabla products... ";
        $db->exec("ALTER TABLE products ADD COLUMN sizes VARCHAR(255) DEFAULT 'S,M,L' AFTER price");
        echo "OK<br>";
    } else {
        echo "Tabla 'products' ya tiene columna 'sizes'.<br>";
    }

    // 2. Verificar tabla logs
    try {
        $db->query("SELECT 1 FROM logs LIMIT 1");
        echo "Tabla 'logs' existe.<br>";
    } catch (PDOException $e) {
        echo "Tabla 'logs' NO existe. Creando...<br>";
        $sql = "CREATE TABLE logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            action VARCHAR(50),
            description TEXT,
            ip_address VARCHAR(45),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $db->exec($sql);
        echo "Tabla 'logs' creada.<br>";
    }

    // 3. Verificar tabla rate_history
    try {
        $db->query("SELECT 1 FROM rate_history LIMIT 1");
        echo "Tabla 'rate_history' existe.<br>";
    } catch (PDOException $e) {
        echo "Tabla 'rate_history' NO existe. Creando...<br>";
        $sql = "CREATE TABLE rate_history (
            id INT AUTO_INCREMENT PRIMARY KEY,
            rate DECIMAL(10,2) NOT NULL,
            user_id INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $db->exec($sql);
        echo "Tabla 'rate_history' creada.<br>";
    }

    // 4. Verificar tabla order_items
    try {
        $db->query("SELECT 1 FROM order_items LIMIT 1");
        echo "Tabla 'order_items' existe.<br>";
    } catch (PDOException $e) {
        echo "Tabla 'order_items' NO existe. Creando...<br>";
        $sql = "CREATE TABLE order_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            product_id INT NOT NULL,
            quantity INT NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id)
        )";
        $db->exec($sql);
        echo "Tabla 'order_items' creada.<br>";
    }

    echo "<br><b>reparación finalizada. Intenta hacer el pedido nuevamente.</b>";

} catch (Exception $e) {
    echo "Error Fatal: " . $e->getMessage();
}
?>
