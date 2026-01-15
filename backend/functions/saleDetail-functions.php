<?php
require_once '../config/db_connect.php';

function createSaleDetails($saleId, $itemId, $quantity, $finalPrice) {
    global $conn;
    
    $query = "INSERT INTO saledetails (SaleID, ItemID, Quantity, FinalPrice) VALUES (:1, :2, :3, :4)";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $saleId);
    oci_bind_by_name($stmt, ':2', $itemId);
    oci_bind_by_name($stmt, ':3', $quantity);
    oci_bind_by_name($stmt, ':4', $finalPrice);

    $result = oci_execute($stmt);

    if ($result) {
        oci_free_statement($stmt);
        return ['status' => true, 'message' => 'Sale details created successfully.'];
    } else {
        $error = oci_error($stmt);
        oci_free_statement($stmt);
        return ['status' => false, 'message' => 'Failed to create sale details: ' . $error['message']];
    }
}

function getSaleDetailsBySaleId($saleId) {
    global $conn;

    $query = "SELECT SaleDetailID, SaleID, ItemID, Quantity, FinalPrice FROM saledetails WHERE SaleID = :1";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $saleId);
    $result = oci_execute($stmt);

    $saleDetails = [];
    if ($result) {
        while ($row = oci_fetch_assoc($stmt)) {
            $saleDetails[] = $row;
        }
    }
    oci_free_statement($stmt);

    return $saleDetails;
}

function editSaleDetails($saleDetailId, $saleId, $itemId, $quantity, $finalPrice) {
    global $conn;

    $query = "UPDATE saledetails SET SaleID = :1, ItemID = :2, Quantity = :3, FinalPrice = :4 WHERE SaleDetailID = :5";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $saleId);
    oci_bind_by_name($stmt, ':2', $itemId);
    oci_bind_by_name($stmt, ':3', $quantity);
    oci_bind_by_name($stmt, ':4', $finalPrice);
    oci_bind_by_name($stmt, ':5', $saleDetailId);

    $result = oci_execute($stmt);

    if ($result) {
        oci_free_statement($stmt);
        return ['status' => true, 'message' => 'Sale details updated successfully.'];
    } else {
        $error = oci_error($stmt);
        oci_free_statement($stmt);
        return ['status' => false, 'message' => 'Failed to update sale details: ' . $error['message']];
    }
}

function deleteSaleDetails($saleDetailId) {
    global $conn;

    $query = "DELETE FROM saledetails WHERE SaleDetailID = :1";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $saleDetailId);

    $result = oci_execute($stmt);

    if ($result) {
        oci_free_statement($stmt);
        return ['status' => true, 'message' => 'Sale details deleted successfully.'];
    } else {
        $error = oci_error($stmt);
        oci_free_statement($stmt);
        return ['status' => false, 'message' => 'Failed to delete sale details: ' . $error['message']];
    }
}
?>