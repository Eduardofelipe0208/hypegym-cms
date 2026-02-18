<?php
/**
 * API Orders - HYPE Sportswear CMS
 * Endpoint para listar pedidos con filtros avanzados
 */

header('Content-Type: application/json');
require_once '../includes/db.php';

session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

try {
    $db = getDB();
    
    // Parámetros de Filtro
    $status = $_GET['status'] ?? '';
    $dateStart = $_GET['date_start'] ?? '';
    $dateEnd = $_GET['date_end'] ?? '';
    $method = $_GET['payment_method'] ?? '';
    $minAmount = $_GET['min_amount'] ?? '';
    $maxAmount = $_GET['max_amount'] ?? '';
    $search = $_GET['search'] ?? '';

    // Construcción de Query Dinámica
    $sql = "
        SELECT 
            o.*, 
            (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as items_count
        FROM orders o 
        WHERE 1=1
    ";
    
    $params = [];

    // Filtro por Estado
    if (!empty($status)) {
        $sql .= " AND o.status = ?";
        $params[] = $status;
    }

    // Filtro por Fechas
    if (!empty($dateStart)) {
        $sql .= " AND DATE(o.created_at) >= ?";
        $params[] = $dateStart;
    }
    if (!empty($dateEnd)) {
        $sql .= " AND DATE(o.created_at) <= ?";
        $params[] = $dateEnd;
    }

    // Filtro por Método de Pago
    if (!empty($method)) {
        $sql .= " AND o.payment_method_id = ?";
        $params[] = $method;
    }

    // Filtro por Monto
    if (!empty($minAmount)) {
        $sql .= " AND o.total_amount >= ?";
        $params[] = $minAmount;
    }
    if (!empty($maxAmount)) {
        $sql .= " AND o.total_amount <= ?";
        $params[] = $maxAmount;
    }

    // Búsqueda (Nombre, Referencia, ID)
    if (!empty($search)) {
        $sql .= " AND (o.customer_name LIKE ? OR o.payment_reference LIKE ? OR o.id LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }

    // Ordenamiento por defecto
    $sql .= " ORDER BY o.created_at DESC LIMIT 100";

    // Ejecutar
    $orders = dbQuery($sql, $params);

    // Formatear respuesta (IDs de estado a texto legible, etc)
    $formattedOrders = array_map(function($order) {
        $statusLabels = [
            'pending' => 'Pendiente',
            'paid' => 'Pagado',
            'shipped' => 'Enviado',
            'rejected' => 'Rechazado'
        ];
        
        $order['status_label'] = $statusLabels[$order['status']] ?? ucfirst($order['status']);
        $order['total_bs'] = $order['total_amount'] * ($order['exchange_rate'] > 0 ? $order['exchange_rate'] : 1);
        
        return $order;
    }, $orders);

    echo json_encode(['data' => $formattedOrders]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
