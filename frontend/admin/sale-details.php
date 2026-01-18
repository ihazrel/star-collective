<?php
require_once __DIR__ . '/../../backend/config/db_connect.php';
require_once __DIR__ . '/../../backend/functions/sale-functions.php';
require_once __DIR__ . '/../../backend/functions/saleDetail-functions.php';
require_once __DIR__ . '/../../backend/functions/item-functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['saleId'])) {
    $saleId = $_POST['saleId'];
    
    // Get sale information
    $sale = getSaleById($saleId);
    
    if (!$sale) {
        echo json_encode(['status' => false, 'message' => 'Sale not found']);
        exit;
    }
    
    // Get sale details with item information
    $saleDetails = getSaleDetailsBySaleId($saleId);
    
    // Enrich sale details with item names
    $items = [];
    foreach ($saleDetails as $detail) {
        $item = getItemById($detail['ITEMID']);
        $items[] = [
            'name' => $item ? $item['NAME'] : 'Unknown Item',
            'quantity' => $detail['QUANTITY'],
            'finalPrice' => number_format($detail['FINALPRICE'], 2)
        ];
    }
    
    // Get customer and staff info from the already joined query
    $allSales = getAllSales();
    $saleInfo = null;
    foreach ($allSales as $s) {
        if ($s['SALEID'] == $saleId) {
            $saleInfo = $s;
            break;
        }
    }
    
    echo json_encode([
        'status' => true,
        'saleId' => $saleId,
        'dateTime' => $saleInfo['SALEDATETIME'] ?? 'N/A',
        'totalPrice' => number_format($sale['TOTALPRICE'], 2),
        'customer' => $saleInfo['CUSTOMER_NAME'] ?? 'N/A',
        'staff' => $saleInfo['STAFF_NAME'] ?? 'N/A',
        'items' => $items
    ]);
} else {
    echo json_encode(['status' => false, 'message' => 'Invalid request']);
}