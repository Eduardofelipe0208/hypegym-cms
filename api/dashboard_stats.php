<?php
/**
 * API Dashboard Stats
 * Retorna métricas y datos para gráficas en formato JSON
 */

header('Content-Type: application/json');
require_once '../db.php';

// Verificar sesión de admin (opcional, pero recomendado)
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

try {
    $db = getDB();
    $response = [];

    // 1. Métricas Principales (Cards)
    
    // Total Pedidos
    $totalOrders = dbQueryOne("SELECT COUNT(*) as total FROM orders");
    $response['total_orders'] = $totalOrders['total'];

    // Ingresos Totales (USD) - Solo pedidos completados
    $incomeUsd = dbQueryOne("SELECT SUM(total_amount) as total FROM orders WHERE status = 'completed'");
    $response['income_usd'] = $incomeUsd['total'] ?? 0;

    // Ingresos Totales (Bs) - Usando tasa configurada
    $tasaQuery = dbQueryOne("SELECT value FROM settings WHERE `key` = 'tasa_bcv'");
    $tasa = $tasaQuery ? floatval($tasaQuery['value']) : 36.50; // Fallback
    $response['income_bs'] = $response['income_usd'] * $tasa;
    $response['exchange_rate'] = $tasa;

    // Pedidos Pendientes
    $pendingOrders = dbQueryOne("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'");
    $response['pending_orders'] = $pendingOrders['total'];

    // 2. Datos para Gráficas

    // Ventas últimos 7 días
    $salesLast7Days = dbQuery("
        SELECT DATE(created_at) as date, SUM(total_amount) as total 
        FROM orders 
        WHERE created_at >= DATE(NOW()) - INTERVAL 7 DAY 
        AND status = 'completed'
        GROUP BY DATE(created_at)
        ORDER BY date ASC
    ");
    $response['sales_last_7_days'] = $salesLast7Days;

    // Ventas por mes (Año actual)
    $salesByMonth = dbQuery("
        SELECT DATE_FORMAT(created_at, '%Y-%m') as month, SUM(total_amount) as total
        FROM orders
        WHERE status = 'completed' AND YEAR(created_at) = YEAR(NOW())
        GROUP BY month
        ORDER BY month ASC
    ");
    $response['sales_by_month'] = $salesByMonth;

    // Productos más vendidos
    // Nota: Requiere unir con order_items y products. 
    // Si no hay items, mostraremos array vacío para evitar error.
    try {
        $topProducts = dbQuery("
            SELECT p.name, SUM(oi.quantity) as total_sold
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            JOIN orders o ON oi.order_id = o.id
            WHERE o.status = 'completed'
            GROUP BY p.id
            ORDER BY total_sold DESC
            LIMIT 5
        ");
        $response['top_products'] = $topProducts;
    } catch (Exception $e) {
        $response['top_products'] = []; // Fallback si la tabla order_items está vacía
    }

    // 3. Actividad Reciente (Últimos 5 pedidos)
    $recentOrders = dbQuery("
        SELECT id, customer_name, total_amount, status, created_at 
        FROM orders 
        ORDER BY created_at DESC 
        LIMIT 5
    ");
    $response['recent_orders'] = $recentOrders;

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
