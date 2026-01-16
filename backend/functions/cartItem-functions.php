<?php
require_once  __DIR__ . '/../config/db_connect.php';

function createCartItem($CustId, $ItemId, $Quantity = 1) {
    global $conn;

    $CustId = (int)$CustId;
    $ItemId = (int)$ItemId;
    $Quantity = (int)$Quantity;
    
    $query = "INSERT INTO CARTITEM (CUSTOMERID, ITEMID, QUANTITY) VALUES (:1, :2, :3)";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $CustId);
    oci_bind_by_name($stmt, ':2', $ItemId);
    oci_bind_by_name($stmt, ':3', $Quantity);

    $result = oci_execute($stmt);

    if ($result) {
        oci_free_statement($stmt);
        return ['status' => true, 'message' => 'Cart item created successfully.'];
    } else {
        $error = oci_error($stmt);
        oci_free_statement($stmt);
        return ['status' => false, 'message' => 'Failed to create cart item: ' . $error['message']];
    }
}

function deleteCartItem($cartId) {
    global $conn;

    $query = "DELETE FROM CARTITEM WHERE CARTID = :1";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $cartId);

    $result = oci_execute($stmt);

    if ($result) {
        oci_free_statement($stmt);
        return ['status' => true, 'message' => 'Cart item deleted successfully.'];
    } else {
        $error = oci_error($stmt);
        oci_free_statement($stmt);
        return ['status' => false, 'message' => 'Failed to delete cart item: ' . $error['message']];
    }
}

function getCartItemsByCustomer($CustId) {
    global $conn;

    $query = "SELECT ITEM.NAME, SUM(ITEM.PRICE) OVER() AS TOTAL_PRICE, CARTITEM.QUANTITY FROM CARTITEM JOIN ITEM ON CARTITEM.ITEMID = ITEM.ITEMID WHERE CARTITEM.CUSTOMERID = :1";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $CustId);

    oci_execute($stmt);

    $cartItems = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $cartItems[] = $row;
    }

    oci_free_statement($stmt);
    return $cartItems;
}
?>