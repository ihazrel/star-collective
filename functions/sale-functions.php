<?php
require_once '../config/db_connect.php';

function createSale($saleDateTime, $totalPrice, $customerId, $staffId) {
    global $conn;
    
    $query = "INSERT INTO sales (SaleDateTime, TotalPrice, CustomerID, StaffID) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'sdii', $saleDateTime, $totalPrice, $customerId, $staffId);

    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        return ['status' => true, 'message' => 'Sale created successfully.'];
    } else {
        return ['status' => false, 'message' => 'Failed to create sale.'];
    }
}

function getAllSales() {
    global $conn;

    $query = "SELECT SaleID, SaleDateTime, TotalPrice, CustomerID, StaffID FROM sales";
    $result = mysqli_query($conn, $query);

    $sales = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $sales[] = $row;
    }

    return $sales;
}

function getSaleById($saleId) {
    global $conn;

    $query = "SELECT SaleID, SaleDateTime, TotalPrice, CustomerID, StaffID FROM sales WHERE SaleID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $saleId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_assoc($result);
}

function editSale($saleId, $saleDateTime, $totalPrice, $customerId, $staffId) {
    global $conn;

    $query = "UPDATE sales SET SaleDateTime = ?, TotalPrice = ?, CustomerID = ?, StaffID = ? WHERE SaleID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'sdiii', $saleDateTime, $totalPrice, $customerId, $staffId, $saleId);

    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        return ['status' => true, 'message' => 'Sale updated successfully.'];
    } else {
        return ['status' => false, 'message' => 'Failed to update sale.'];
    }
}

function deleteSale($saleId) {
    global $conn;

    $query = "DELETE FROM sales WHERE SaleID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $saleId);

    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        return ['status' => true, 'message' => 'Sale deleted successfully.'];
    } else {
        return ['status' => false, 'message' => 'Failed to delete sale.'];
    }
}
?>