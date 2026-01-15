<?php
require_once '../config/db_connect.php';

function createItem($itemName, $price, $stockQuantity, $lastUpdateDateTime) {
    global $conn;
    
    $query = "INSERT INTO items (ItemName, Price, StockQuantity, LastUpdateDateTime) VALUES (:1, :2, :3, :4)";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $itemName);
    oci_bind_by_name($stmt, ':2', $price);
    oci_bind_by_name($stmt, ':3', $stockQuantity);
    oci_bind_by_name($stmt, ':4', $lastUpdateDateTime);

    $result = oci_execute($stmt);

    if ($result) {
        oci_free_statement($stmt);
        return ['status' => true, 'message' => 'Item created successfully.'];
    } else {
        $error = oci_error($stmt);
        oci_free_statement($stmt);
        return ['status' => false, 'message' => 'Failed to create item: ' . $error['message']];
    }
}

function getAllItems() {
    global $conn;
    
    $query = "SELECT * FROM items";
    $stmt = oci_parse($conn, $query);
    $result = oci_execute($stmt);
    
    $items = [];
    if ($result) {
        while ($row = oci_fetch_assoc($stmt)) {
            $items[] = $row;
        }
    }
    oci_free_statement($stmt);
    
    return $items;
}

function getItemById($itemId) {
    global $conn;
    
    $query = "SELECT * FROM items WHERE ItemID = :1";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $itemId);
    $result = oci_execute($stmt);
    
    $item = null;
    if ($result) {
        $item = oci_fetch_assoc($stmt);
    }
    oci_free_statement($stmt);
    
    return $item ? $item : null;
}

function editItem($itemId, $itemName, $price, $stockQuantity, $lastUpdateDateTime) {
    global $conn;
    
    $query = "UPDATE items SET ItemName = :1, Price = :2, StockQuantity = :3, LastUpdateDateTime = :4 WHERE ItemID = :5";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $itemName);
    oci_bind_by_name($stmt, ':2', $price);
    oci_bind_by_name($stmt, ':3', $stockQuantity);
    oci_bind_by_name($stmt, ':4', $lastUpdateDateTime);
    oci_bind_by_name($stmt, ':5', $itemId);

    $result = oci_execute($stmt);

    if ($result) {
        oci_free_statement($stmt);
        return ['status' => true, 'message' => 'Item updated successfully.'];
    } else {
        $error = oci_error($stmt);
        oci_free_statement($stmt);
        return ['status' => false, 'message' => 'Failed to update item: ' . $error['message']];
    }
}

function deleteItem($itemId) {
    global $conn;
    
    $query = "DELETE FROM items WHERE ItemID = :1";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $itemId);

    $result = oci_execute($stmt);

    if ($result) {
        oci_free_statement($stmt);
        return ['status' => true, 'message' => 'Item deleted successfully.'];
    } else {
        $error = oci_error($stmt);
        oci_free_statement($stmt);
        return ['status' => false, 'message' => 'Failed to delete item: ' . $error['message']];
    }
}
?>