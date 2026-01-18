<?php
require_once __DIR__ . '/../../backend/config/db_connect.php';
require_once __DIR__ . '/../../backend/functions/purchaseOrder-functions.php';

$poId = $_POST['purchaseOrderId'] ?? null;
if (!$poId) exit;

$po = getPurchaseOrderById($poId);
$items = getPurchaseOrderDetails($poId);

$statusClass = match(strtolower($po['STATUS'])) {
    'pending' => 'bg-warning text-dark',
    'completed' => 'bg-success',
    'cancelled' => 'bg-danger',
    default => 'bg-secondary'
};

echo json_encode([
    'invoice' => $po['INVOICENO'],
    'vendor' => $po['COMPANYNAME'],
    'total' => number_format($po['TOTALPRICE'], 2),
    'status' => ucfirst($po['STATUS']),
    'statusClass' => $statusClass,
    'items' => array_map(fn($i) => [
        'name' => $i['ITEMNAME'],
        'qty' => $i['QUANTITY']
    ], $items),
    'purchaseOrderId' => $po['PURCHASEORDERID']
]);
