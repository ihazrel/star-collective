<?php
require_once '../config/db_connect.php';

function createSale($saleDateTime, $totalPrice, $customerId, $staffId) {
    global $conn;
    
    $query = "INSERT INTO sales (SaleDateTime, TotalPrice, CustomerID, StaffID) VALUES (:1, :2, :3, :4)";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $saleDateTime);
    oci_bind_by_name($stmt, ':2', $totalPrice);
    oci_bind_by_name($stmt, ':3', $customerId);
    oci_bind_by_name($stmt, ':4', $staffId);

    $result = oci_execute($stmt);

    if ($result) {
        oci_free_statement($stmt);
        return ['status' => true, 'message' => 'Sale created successfully.'];
    } else {
        $error = oci_error($stmt);
        oci_free_statement($stmt);
        return ['status' => false, 'message' => 'Failed to create sale: ' . $error['message']];
    }
}

function getAllSales() {
    global $conn;

    $query = "SELECT SaleID, SaleDateTime, TotalPrice, CustomerID, StaffID FROM sales";
    $stmt = oci_parse($conn, $query);
    $result = oci_execute($stmt);

    $sales = [];
    if ($result) {
        while ($row = oci_fetch_assoc($stmt)) {
            $sales[] = $row;
        }
    }
    oci_free_statement($stmt);

    return $sales;
}

function getSaleById($saleId) {
    global $conn;

    $query = "SELECT SaleID, SaleDateTime, TotalPrice, CustomerID, StaffID FROM sales WHERE SaleID = :1";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $saleId);
    $result = oci_execute($stmt);

    $row = null;
    if ($result) {
        $row = oci_fetch_assoc($stmt);
    }
    oci_free_statement($stmt);

    return $row;
}

function editSale($saleId, $saleDateTime, $totalPrice, $customerId, $staffId) {
    global $conn;

    $query = "UPDATE sales SET SaleDateTime = :1, TotalPrice = :2, CustomerID = :3, StaffID = :4 WHERE SaleID = :5";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $saleDateTime);
    oci_bind_by_name($stmt, ':2', $totalPrice);
    oci_bind_by_name($stmt, ':3', $customerId);
    oci_bind_by_name($stmt, ':4', $staffId);
    oci_bind_by_name($stmt, ':5', $saleId);

    $result = oci_execute($stmt);

    if ($result) {
        oci_free_statement($stmt);
        return ['status' => true, 'message' => 'Sale updated successfully.'];
    } else {
        $error = oci_error($stmt);
        oci_free_statement($stmt);
        return ['status' => false, 'message' => 'Failed to update sale: ' . $error['message']];
    }
}

function deleteSale($saleId) {
    global $conn;

    $query = "DELETE FROM sales WHERE SaleID = :1";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $saleId);

    $result = oci_execute($stmt);

    if ($result) {
        oci_free_statement($stmt);
        return ['status' => true, 'message' => 'Sale deleted successfully.'];
    } else {
        $error = oci_error($stmt);
        oci_free_statement($stmt);
        return ['status' => false, 'message' => 'Failed to delete sale: ' . $error['message']];
    }
}
?>