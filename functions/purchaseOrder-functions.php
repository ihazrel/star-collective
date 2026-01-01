<?php
require_once '../config/db_connect.php';

function createPurchaseOrder($vendorId, $staffId, $orderDateTime, $invoiceNumber, $totalPrice) {
    global $conn;
    
    $query = "INSERT INTO purchase_orders (VendorID, StaffID, OrderDateTime, InvoiceNumber, TotalPrice) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'iissi', $vendorId, $staffId, $orderDateTime, $invoiceNumber, $totalPrice);

    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        return ['status' => true, 'message' => 'Purchase order created successfully.'];
    } else {
        return ['status' => false, 'message' => 'Failed to create purchase order.'];
    }
}

function getAllPurchaseOrders() {
    global $conn;

    $query = "SELECT PurchaseOrderID, VendorID, StaffID, OrderDateTime, InvoiceNumber, TotalPrice FROM purchase_orders";
    $result = mysqli_query($conn, $query);

    $purchaseOrders = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $purchaseOrders[] = $row;
    }

    return $purchaseOrders;
}

function getPurchaseOrderById($purchaseOrderId) {
    global $conn;

    $query = "SELECT PurchaseOrderID, VendorID, StaffID, OrderDateTime, InvoiceNumber, TotalPrice FROM purchase_orders WHERE PurchaseOrderID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $purchaseOrderId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_assoc($result);
}

function editPurchaseOrder($purchaseOrderId, $vendorId, $staffId, $orderDateTime, $invoiceNumber, $totalPrice) {
    global $conn;

    $query = "UPDATE purchase_orders SET VendorID = ?, StaffID = ?, OrderDateTime = ?, InvoiceNumber = ?, TotalPrice = ? WHERE PurchaseOrderID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'iissii', $vendorId, $staffId, $orderDateTime, $invoiceNumber, $totalPrice, $purchaseOrderId);

    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        return ['status' => true, 'message' => 'Purchase order updated successfully.'];
    } else {
        return ['status' => false, 'message' => 'Failed to update purchase order.'];
    }
}

function deletePurchaseOrder($purchaseOrderId) {
    global $conn;

    $query = "DELETE FROM purchase_orders WHERE PurchaseOrderID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $purchaseOrderId);

    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        return ['status' => true, 'message' => 'Purchase order deleted successfully.'];
    } else {
        return ['status' => false, 'message' => 'Failed to delete purchase order.'];
    }
}
?>