<?php
/**
 * Export Orders to Excel (CSV)
 * Genera un archivo CSV compatible con Excel
 */

require_once '../../includes/db.php';

// Validar Sesión
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login.php');
    exit;
}

// Configurar Cabeceras para Descarga
$filename = "pedidos_hype_" . date('Y-m-d_H-i') . ".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Crear puntero de salida
$output = fopen('php://output', 'w');

// BOM para que Excel reconozca UTF-8 (Caralibro hack)
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Cabeceras de Columnas
fputcsv($output, [
    'ID Pedido', 
    'Fecha', 
    'Cliente', 
    'Teléfono', 
    'Dirección', 
    'Productos', 
    'Total USD', 
    'Total Bs', 
    'Tasa', 
    'Método Pago', 
    'Referencia', 
    'Estado'
]);

// --- CONSTRUCCIÓN DE CONSULTA ---
$sql = "
    SELECT 
        o.id,
        o.created_at,
        o.customer_name,
        o.customer_phone,
        o.customer_address,
        o.total_amount,
        o.exchange_rate,
        o.payment_method,
        o.payment_reference,
        o.status,
        GROUP_CONCAT(CONCAT(p.name, ' (x', oi.quantity, ')') SEPARATOR ' | ') as products_list
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    LEFT JOIN products p ON oi.product_id = p.id
    WHERE 1=1
";

$params = [];

// Filtros
if (!empty($_GET['status'])) {
    $sql .= " AND o.status = ?";
    $params[] = $_GET['status'];
}

if (!empty($_GET['date_start'])) {
    $sql .= " AND DATE(o.created_at) >= ?";
    $params[] = $_GET['date_start'];
}

if (!empty($_GET['date_end'])) {
    $sql .= " AND DATE(o.created_at) <= ?";
    $params[] = $_GET['date_end'];
}

if (!empty($_GET['payment_method'])) {
    $sql .= " AND o.payment_method_id = ?";
    $params[] = $_GET['payment_method'];
}

if (!empty($_GET['search'])) {
    $sql .= " AND (o.customer_name LIKE ? OR o.payment_reference LIKE ? OR o.id LIKE ?)";
    $searchTerm = "%" . $_GET['search'] . "%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

$sql .= " GROUP BY o.id ORDER BY o.id DESC";

try {
    $db = getDB();
    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    // Iterar y escribir filas (Memoria eficiente)
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Cálculo de Bs (Total USD * Tasa guardada)
        $rate = floatval($row['exchange_rate'] > 0 ? $row['exchange_rate'] : 1);
        $totalBs = floatval($row['total_amount']) * $rate;

        // Formateo de Estado
        $statusMap = [
            'pending' => 'Pendiente',
            'paid' => 'Pagado',
            'rejected' => 'Rechazado',
            'shipped' => 'Enviado'
        ];
        $status = $statusMap[$row['status']] ?? $row['status'];

        fputcsv($output, [
            $row['id'],
            $row['created_at'],
            $row['customer_name'],
            $row['customer_phone'],
            $row['customer_address'],
            $row['products_list'],
            number_format($row['total_amount'], 2, ',', '.'), // Formato Excel numero
            number_format($totalBs, 2, ',', '.'),
            number_format($rate, 2, ',', '.'),
            $row['payment_method'],
            $row['payment_reference'],
            $status
        ]);
    }

} catch (PDOException $e) {
    // En caso de error, escribir en el CSV para que el usuario lo vea
    fputcsv($output, ['ERROR DE SISTEMA', $e->getMessage()]);
}

fclose($output);
exit;
