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

function getAllItems() {
    global $conn;
    
    $query = "SELECT * FROM items";
    $result = mysqli_query($conn, $query);
    
    $items = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }
    
    return $items;
}

function getItemById($itemId) {
    global $conn;
    
    $query = "SELECT * FROM items WHERE ItemID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $itemId);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $item = mysqli_fetch_assoc($result);
    
    return $item ? $item : null;
}

function editItem($itemId, $itemName, $price, $stockQuantity, $lastUpdateDateTime) {
    global $conn;
    
    $query = "UPDATE items SET ItemName = ?, Price = ?, StockQuantity = ?, LastUpdateDateTime = ? WHERE ItemID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'sdiii', $itemName, $price, $stockQuantity, $lastUpdateDateTime, $itemId);

    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        return ['status' => true, 'message' => 'Item updated successfully.'];
    } else {
        return ['status' => false, 'message' => 'Failed to update item.'];
    }
}

function deleteItem($itemId) {
    global $conn;
    
    $query = "DELETE FROM items WHERE ItemID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $itemId);

    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        return ['status' => true, 'message' => 'Item deleted successfully.'];
    } else {
        return ['status' => false, 'message' => 'Failed to delete item.'];
    }
}
?>