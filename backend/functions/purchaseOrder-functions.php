<?php
require_once __DIR__ . '/../config/db_connect.php';
session_start();

function createPurchaseOrder($vendorId, $totalPrice, $poItems) {
    global $conn;

    // Generate new invoice number
    $newInvoice = createInvoiceNumber();
    $staffId = $_SESSION['user_id'];
    
    // Insert into PURCHASEORDER table
    $query = "INSERT INTO PURCHASEORDER (VENDORID, STAFFID, ORDERDATETIME, INVOICENO, TOTALPRICE, STATUS) VALUES (:1, :2, SYSDATE, :4, :5, 'Pending')";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $vendorId);
    oci_bind_by_name($stmt, ':2', $staffId);
    oci_bind_by_name($stmt, ':4', $newInvoice);
    oci_bind_by_name($stmt, ':5', $totalPrice);

    $result = oci_execute($stmt);
    
    if(!$result) {
        $error = oci_error($stmt);
        oci_free_statement($stmt);
        return ['status' => false, 'message' => 'Failed to create purchase order: ' . $error['message']];
    }

    // Get the latest PURCHASEORDERID
    $purchaseOrderId = getLatestPurchaseOrderId();

    // Insert each item into PURCHASEORDERDETAIL table
    foreach ($poItems as $item) {
        $itemId = $item['id'];
        $quantity = $item['quantity'];
        createPurchaseOrderDetail($purchaseOrderId, $itemId, $quantity);
    }

    if ($result) {
        return ['status' => true, 'message' => 'Purchase order created successfully.'];
    } else {
        $error = oci_error($stmt);
        return ['status' => false, 'message' => 'Failed to create purchase order: ' . $error['message']];
    }

    oci_free_statement($stmt);
}

function createPurchaseOrderDetail($purchaseOrderId, $itemId, $quantity) {
    global $conn;
    
    $query = "INSERT INTO PURCHASEORDERDETAIL (PURCHASEORDERID, ITEMID, QUANTITY) VALUES (:1, :2, :3)";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $purchaseOrderId);
    oci_bind_by_name($stmt, ':2', $itemId);
    oci_bind_by_name($stmt, ':3', $quantity);

    $result = oci_execute($stmt);

    if ($result) {
        oci_free_statement($stmt);
        return ['status' => true, 'message' => 'Purchase order detail created successfully.'];
    } else {
        $error = oci_error($stmt);
        oci_free_statement($stmt);
        return ['status' => false, 'message' => 'Failed to create purchase order detail: ' . $error['message']];
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

function getLatestPurchaseOrderId() {
    global $conn;

    $query = "SELECT MAX(PURCHASEORDERID) AS LATEST_ID FROM PURCHASEORDER";
    $stmt = oci_parse($conn, $query);
    oci_execute($stmt);

    $row = oci_fetch_assoc($stmt);
    oci_free_statement($stmt);

    return $row['LATEST_ID'] ?? null;
}

function getLatestPurchaseOrder() {
    global $conn;

    $query = "SELECT PURCHASEORDER.INVOICENO, VENDOR.COMPANYNAME, PURCHASEORDER.TOTALPRICE FROM PURCHASEORDER LEFT JOIN VENDOR ON PURCHASEORDER.VENDORID = VENDOR.VENDORID ORDER BY PURCHASEORDER.PURCHASEORDERID DESC FETCH FIRST 3 ROWS ONLY";
    $stmt = oci_parse($conn, $query);
    $result = oci_execute($stmt);

    $latestPurchaseOrders = [];
    if ($result) {
        while ($row = oci_fetch_assoc($stmt)) {
            $latestPurchaseOrders[] = $row;
        }
    }
    oci_free_statement($stmt);

    return $latestPurchaseOrders;
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

function createInvoiceNumber() {
    global $conn;

    $query = "SELECT 'PO-' || TO_CHAR(SYSDATE, 'YYYY') || '-' || LPAD(NVL(MAX(TO_NUMBER(SUBSTR(INVOICENO, 9))), 0) + 1,3, '0') AS INVOICENO FROM PURCHASEORDER WHERE INVOICENO LIKE 'PO-' || TO_CHAR(SYSDATE, 'YYYY') || '-%';";
    $stmt = oci_parse($conn, $query);
    oci_execute($stmt);

    $row = oci_fetch_assoc($stmt);
    oci_free_statement($stmt);

    return $row['INVOICENO'] ?? null;
}
?>