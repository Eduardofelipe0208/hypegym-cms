<?php
/**
 * API Checkout - HYPE Sportswear CMS
 * Procesa nuevos pedidos
 */

header('Content-Type: application/json');
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        throw new Exception('Datos inválidos');
    }

    $db = getDB();
    
    // Iniciar Transacción
    $db->beginTransaction();

    // 1. Obtener Tasa de Cambio Actual (Snapshot)
    $tasaQuery = dbQueryOne("SELECT value FROM settings WHERE `key` = 'tasa_bcv'");
    $exchangeRate = $tasaQuery ? floatval($tasaQuery['value']) : 36.50;

    // 3. Calcular Montos
    $calculatedTotalUSD = 0;
    
    foreach ($input['cart'] as $item) {
        $product = dbQueryOne("SELECT price, stock FROM products WHERE id = ?", [$item['id']]);
        if (!$product) throw new Exception("Producto ID {$item['id']} no encontrado");
        
        $calculatedTotalUSD += floatval($product['price']) * intval($item['quantity']);
    }

    $totalBS = $calculatedTotalUSD * $exchangeRate;

    // Ejecutar Insert Orden
    // Asumimos que 'customer_email' no es crítico para este flujo manual, usamos un placeholder o input opcional
    $email = $input['email'] ?? 'cliente@hypesportswear.com'; 

    $sqlOrder = "INSERT INTO orders (customer_name, customer_email, customer_phone, customer_address, total_amount, total_amount_bs, status, payment_reference, exchange_rate, payment_method, notes) VALUES (?, ?, ?, ?, ?, ?, 'pending', ?, ?, ?, ?)";

    $orderId = dbExecute($sqlOrder, [
        $input['name'],
        $email,
        $input['phone'],
        $input['address'],
        $calculatedTotalUSD,
        $totalBS, // Nuevo campo
        $input['reference'],
        $exchangeRate,
        $input['payment_method_name'], 
        $input['notes'] ?? ''
    ]);

    if (!$orderId) throw new Exception("Error al crear la orden");

    // 3. Insertar Items de Orden y Actualizar Stock
    $sqlItem = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $sqlStock = "UPDATE products SET stock = stock - ? WHERE id = ?";

    foreach ($input['cart'] as $item) {
        $product = dbQueryOne("SELECT price FROM products WHERE id = ?", [$item['id']]);
        
        // Insert Item
        dbExecute($sqlItem, [$orderId, $item['id'], $item['quantity'], $product['price']]);
        
        // Update Stock
        dbExecute($sqlStock, [$item['quantity'], $item['id']]);
    }

    // Confirmar Transacción
    $db->commit();

    echo json_encode(['success' => true, 'order_id' => $orderId, 'message' => 'Pedido creado exitosamente']);

} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    // LOG ERROR TO FILE
    file_put_contents('error_log.txt', date('Y-m-d H:i:s') . " - Error: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n\n", FILE_APPEND);
    
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
