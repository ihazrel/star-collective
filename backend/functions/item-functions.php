<?php
require_once  __DIR__ . '/../config/db_connect.php';

function getAllItems($OrderColumn = null, $OrderDirection = 'ASC') {
    global $conn;
    
    $query = "SELECT ITEMID, NAME, PRICE, CURRENTSTOCK, TO_CHAR(LASTUPDATEDATETIME, 'DD-MON-YYYY HH24:MI:SS') AS LASTUPDATEDATETIME FROM ITEM ";
    if ($OrderColumn) {
        $query .= "ORDER BY " . $OrderColumn . " " . ($OrderDirection === 'DESC' ? 'DESC' : 'ASC');
    } else {
        $query .= "ORDER BY ITEMID ASC";
    }
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
    
    $query = "SELECT * FROM ITEM WHERE ITEMID = :1";
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

function createItem($itemName, $price, $currentStock) {
    global $conn;
    
    $query = "INSERT INTO ITEM (NAME, PRICE, CURRENTSTOCK, LASTUPDATEDATETIME) VALUES (:1, :2, :3, SYSDATE)";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $itemName);
    oci_bind_by_name($stmt, ':2', $price);
    oci_bind_by_name($stmt, ':3', $currentStock);

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

function editItem($itemId, $itemName, $price, $currentStock) {
    global $conn;
    
    $query = "UPDATE ITEM SET NAME = :1, PRICE = :2, CURRENTSTOCK = :3, LASTUPDATEDATETIME = SYSDATE WHERE ITEMID = :5";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $itemName);
    oci_bind_by_name($stmt, ':2', $price);
    oci_bind_by_name($stmt, ':3', $currentStock);
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
    
    $query = "DELETE FROM ITEM WHERE ITEMID = :1";
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

//business rules
function getItemWithStockBelow($threshold) {
    global $conn;
    
    $query = "SELECT ITEMID, NAME, PRICE, CURRENTSTOCK FROM ITEM WHERE CURRENTSTOCK < :1 ORDER BY CURRENTSTOCK ASC";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $threshold);
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

function updateStock($itemId, $increment) {
    global $conn;

    $item = getItemById($itemId);
    if (!$item) {
        return ['status' => false, 'message' => 'Item not found.'];
    }
    $newStock = $item['CURRENTSTOCK'] + $increment;
    if ($newStock < 0) {
        return ['status' => false, 'message' => 'Stock cannot be negative.'];
    }
    
    $query = "UPDATE ITEM SET CURRENTSTOCK = :1, LASTUPDATEDATETIME = SYSDATE WHERE ITEMID = :2";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $newStock);
    oci_bind_by_name($stmt, ':2', $itemId);

    $result = oci_execute($stmt);

    if ($result) {
        oci_free_statement($stmt);
        return ['status' => true, 'message' => 'Stock updated successfully.'];
    } else {
        $error = oci_error($stmt);
        oci_free_statement($stmt);
        return ['status' => false, 'message' => 'Failed to update stock: ' . $error['message']];
    }
}
?>