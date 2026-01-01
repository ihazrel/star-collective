<?php
require_once '../config/db_connect.php';

function createSaleDetails($saleId, $itemId, $quantity, $finalPrice) {
    global $conn;
    
    $query = "INSERT INTO saledetails (SaleID, ItemID, Quantity, FinalPrice) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'iiid', $saleId, $itemId, $quantity, $finalPrice);

    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        return ['status' => true, 'message' => 'Sale details created successfully.'];
    } else {
        return ['status' => false, 'message' => 'Failed to create sale details.'];
    }
}

function getSaleDetailsBySaleId($saleId) {
    global $conn;

    $query = "SELECT SaleDetailID, SaleID, ItemID, Quantity, FinalPrice FROM saledetails WHERE SaleID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $saleId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $saleDetails = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $saleDetails[] = $row;
    }

    return $saleDetails;
}

function editSaleDetails($saleDetailId, $saleId, $itemId, $quantity, $finalPrice) {
    global $conn;

    $query = "UPDATE saledetails SET SaleID = ?, ItemID = ?, Quantity = ?, FinalPrice = ? WHERE SaleDetailID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'iiidi', $saleId, $itemId, $quantity, $finalPrice, $saleDetailId);

    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        return ['status' => true, 'message' => 'Sale details updated successfully.'];
    } else {
        return ['status' => false, 'message' => 'Failed to update sale details.'];
    }
}

function deleteSaleDetails($saleDetailId) {
    global $conn;

    $query = "DELETE FROM saledetails WHERE SaleDetailID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $saleDetailId);

    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        return ['status' => true, 'message' => 'Sale details deleted successfully.'];
    } else {
        return ['status' => false, 'message' => 'Failed to delete sale details.'];
    }
}
?>