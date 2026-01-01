<?php
require_once '../config/db_connect.php';

function createItem($itemName, $price, $stockQuantity, $lastUpdateDateTime) {
    global $conn;
    
    $query = "INSERT INTO items (ItemName, Price, StockQuantity, LastUpdateDateTime) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'sdii', $itemName, $price, $stockQuantity, $lastUpdateDateTime);

    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        return ['status' => true, 'message' => 'Item created successfully.'];
    } else {
        return ['status' => false, 'message' => 'Failed to create item.'];
    }
}
?>