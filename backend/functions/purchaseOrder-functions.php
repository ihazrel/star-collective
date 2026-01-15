<?php
require_once __DIR__ . '/../config/db_connect.php';

function createPurchaseOrder($vendorId, $staffId, $orderDateTime, $invoiceNumber, $totalPrice) {
    global $conn;
    
    $query = "INSERT INTO purchase_orders (VendorID, StaffID, OrderDateTime, InvoiceNumber, TotalPrice) VALUES (:1, :2, :3, :4, :5)";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $vendorId);
    oci_bind_by_name($stmt, ':2', $staffId);
    oci_bind_by_name($stmt, ':3', $orderDateTime);
    oci_bind_by_name($stmt, ':4', $invoiceNumber);
    oci_bind_by_name($stmt, ':5', $totalPrice);

    $result = oci_execute($stmt);

    if ($result) {
        oci_free_statement($stmt);
        return ['status' => true, 'message' => 'Purchase order created successfully.'];
    } else {
        $error = oci_error($stmt);
        oci_free_statement($stmt);
        return ['status' => false, 'message' => 'Failed to create purchase order: ' . $error['message']];
    }
}

function getAllPurchaseOrders() {
    global $conn;

    $query = "SELECT PurchaseOrderID, VendorID, StaffID, OrderDateTime, InvoiceNumber, TotalPrice FROM purchase_orders";
    $stmt = oci_parse($conn, $query);
    $result = oci_execute($stmt);

    $purchaseOrders = [];
    if ($result) {
        while ($row = oci_fetch_assoc($stmt)) {
            $purchaseOrders[] = $row;
        }
    }
    oci_free_statement($stmt);

    return $purchaseOrders;
}

function getPurchaseOrderById($purchaseOrderId) {
    global $conn;

    $query = "SELECT PurchaseOrderID, VendorID, StaffID, OrderDateTime, InvoiceNumber, TotalPrice FROM purchase_orders WHERE PurchaseOrderID = :1";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $purchaseOrderId);
    $result = oci_execute($stmt);

    $row = null;
    if ($result) {
        $row = oci_fetch_assoc($stmt);
    }
    oci_free_statement($stmt);

    return $row;
}

function editPurchaseOrder($purchaseOrderId, $vendorId, $staffId, $orderDateTime, $invoiceNumber, $totalPrice) {
    global $conn;

    $query = "UPDATE purchase_orders SET VendorID = :1, StaffID = :2, OrderDateTime = :3, InvoiceNumber = :4, TotalPrice = :5 WHERE PurchaseOrderID = :6";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $vendorId);
    oci_bind_by_name($stmt, ':2', $staffId);
    oci_bind_by_name($stmt, ':3', $orderDateTime);
    oci_bind_by_name($stmt, ':4', $invoiceNumber);
    oci_bind_by_name($stmt, ':5', $totalPrice);
    oci_bind_by_name($stmt, ':6', $purchaseOrderId);

    $result = oci_execute($stmt);

    if ($result) {
        oci_free_statement($stmt);
        return ['status' => true, 'message' => 'Purchase order updated successfully.'];
    } else {
        $error = oci_error($stmt);
        oci_free_statement($stmt);
        return ['status' => false, 'message' => 'Failed to update purchase order: ' . $error['message']];
    }
}

function deletePurchaseOrder($purchaseOrderId) {
    global $conn;

    $query = "DELETE FROM purchase_orders WHERE PurchaseOrderID = :1";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $purchaseOrderId);

    $result = oci_execute($stmt);

    if ($result) {
        oci_free_statement($stmt);
        return ['status' => true, 'message' => 'Purchase order deleted successfully.'];
    } else {
        $error = oci_error($stmt);
        oci_free_statement($stmt);
        return ['status' => false, 'message' => 'Failed to delete purchase order: ' . $error['message']];
    }
}
?>