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
        throw new Exception('JSON inválido');
    }

    // Validar Campos Requeridos
    $required = ['cart', 'name', 'phone', 'address', 'payment_method_name'];
    foreach ($required as $field) {
        if (empty($input[$field])) {
            throw new Exception("Campo faltante: $field");
        }
    }

    // Validar Referencia (Opcional si es Efectivo)
    $isCash = stripos($input['payment_method_name'], 'efectivo') !== false;
    if (!$isCash && empty($input['reference'])) {
        throw new Exception("Campo faltante: reference");
    }

    // Sanitizar Strings
    $name = strip_tags(trim($input['name']));
    $phone = strip_tags(trim($input['phone']));
    $address = strip_tags(trim($input['address']));
    $reference = strip_tags(trim($input['reference']));
    $notes = strip_tags(trim($input['notes'] ?? ''));
    $paymentMethod = strip_tags(trim($input['payment_method_name']));
    $email = filter_var($input['email'] ?? '', FILTER_VALIDATE_EMAIL) ?: 'cliente@hypesportswear.com';

    $db = getDB();
    
    // Iniciar Transacción
    $db->beginTransaction();

    // 1. Obtener Tasa de Cambio Actual (Snapshot)
    $tasaQuery = dbQueryOne("SELECT value FROM settings WHERE `key` = 'tasa_bcv'");
    $exchangeRate = $tasaQuery ? floatval($tasaQuery['value']) : 36.50;

    // 3. Calcular Montos y Validar Carrito
    $calculatedTotalUSD = 0;
    
    if (!is_array($input['cart']) || empty($input['cart'])) {
        throw new Exception("El carrito está vacío");
    }

    foreach ($input['cart'] as $item) {
        $imgId = filter_var($item['id'], FILTER_VALIDATE_INT);
        $qty = filter_var($item['quantity'], FILTER_VALIDATE_INT);

        if (!$imgId || !$qty || $qty < 1) {
            throw new Exception("Item inválido en carrito");
        }

        $product = dbQueryOne("SELECT price, stock FROM products WHERE id = ?", [$imgId]);
        if (!$product) throw new Exception("Producto ID {$imgId} no encontrado");
        
        $calculatedTotalUSD += floatval($product['price']) * $qty;
    }

    $totalBS = $calculatedTotalUSD * $exchangeRate;

    // Ejecutar Insert Orden
    $sqlOrder = "INSERT INTO orders (customer_name, customer_email, customer_phone, customer_address, total_amount, total_amount_bs, status, payment_reference, exchange_rate, payment_method, notes) VALUES (?, ?, ?, ?, ?, ?, 'pending', ?, ?, ?, ?)";

    $orderId = dbExecute($sqlOrder, [
        $name,
        $email,
        $phone,
        $address,
        $calculatedTotalUSD,
        $totalBS, // Nuevo campo
        $reference,
        $exchangeRate,
        $paymentMethod,
        $notes
    ]);

    if (!$orderId) throw new Exception("Error al crear la orden");

    // 3. Insertar Items de Orden y Actualizar Stock
    $sqlItem = "INSERT INTO order_items (order_id, product_id, product_name, size, quantity, price) VALUES (?, ?, ?, ?, ?, ?)";
    $sqlStock = "UPDATE products SET stock = stock - ? WHERE id = ?";

    foreach ($input['cart'] as $item) {
        $product = dbQueryOne("SELECT name, price FROM products WHERE id = ?", [$item['id']]);
        
        // Insert Item
        $size = strip_tags($item['size'] ?? ''); // Sanitize size
        dbExecute($sqlItem, [$orderId, $item['id'], $product['name'], $size, $item['quantity'], $product['price']]);
        
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
