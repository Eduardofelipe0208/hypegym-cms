<?php
/**
 * Action: Update Order Status
 * Recibe JSON: { order_id: 123, status: 'completed' }
 */

header('Content-Type: application/json');
require_once '../../includes/db.php';

session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $orderId = isset($input['order_id']) ? intval($input['order_id']) : 0;
    $newStatus = isset($input['status']) ? trim($input['status']) : '';

    $validStatuses = ['pending', 'completed', 'cancelled']; // approved = completed

    if (!$orderId || !in_array($newStatus, $validStatuses)) {
        throw new Exception("Datos inválidos. Estado debe ser: pending, completed, o cancelled.");
    }

    dbExecute("UPDATE orders SET status = ? WHERE id = ?", [$newStatus, $orderId]);

    echo json_encode(['success' => true, 'message' => "Pedido #$orderId actualizado a $newStatus"]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
