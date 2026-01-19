<?php
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/cartItem-functions.php';
require_once __DIR__ . '/saleDetail-functions.php';

function createSale( $totalPrice, $customerId, $staffId, $paymentMethod = '') {
    global $conn;
    
    $query = "INSERT INTO SALE (SALEDATETIME, TOTALPRICE, CUSTOMERID, STAFFID, PAYMENTMETHOD) VALUES (SYSDATE, :1, :2, :3, :4)";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $totalPrice);
    oci_bind_by_name($stmt, ':2', $customerId);
    oci_bind_by_name($stmt, ':3', $staffId);
    oci_bind_by_name($stmt, ':4', $paymentMethod);

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

    $query = "SELECT 
                    SALEID, 
                    TO_CHAR(SALEDATETIME,'DD-MM-YY HH:MI:SS AM') AS SALEDATETIME,
                    TOTALPRICE,
                    CUSTOMERID,
                    STAFFID,
                    CUST.NAME AS CUSTOMER_NAME,
                    COALESCE(STAFF.NAME, 'N/A') AS STAFF_NAME 
                    FROM SALE 
                    LEFT JOIN USERS CUST 
                        ON SALE.CUSTOMERID = CUST.ID 
                    LEFT JOIN USERS STAFF 
                        ON SALE.STAFFID = STAFF.ID 
                    ORDER BY SALEDATETIME DESC";
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

    $query = "SELECT SALEID, TO_CHAR(SALEDATETIME,'DD-MM-YY HH:MI:SS AM') AS SALEDATETIME, TOTALPRICE, CUSTOMERID, STAFFID FROM SALE WHERE SALEID = :1";
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

function getLatestSaleId() {
    global $conn;

    $query = "SELECT MAX(SALEID) AS LATEST_SALE_ID FROM SALE";
    $stmt = oci_parse($conn, $query);
    $result = oci_execute($stmt);

    $latestSaleId = null;
    if ($result) {
        $row = oci_fetch_assoc($stmt);
        $latestSaleId = $row['LATEST_SALE_ID'];
    }
    oci_free_statement($stmt);

    return $latestSaleId;
}

function getPaymentMethodBySaleId($saleId) {
    global $conn;

    $query = "SELECT PAYMENTMETHOD FROM SALE WHERE SALEID = :1";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $saleId);
    $result = oci_execute($stmt);

    $paymentMethod = null;
    if ($result) {
        $row = oci_fetch_assoc($stmt);
        $paymentMethod = $row['PAYMENTMETHOD'];
    }
    oci_free_statement($stmt);

    return $paymentMethod;
}

function editSale($saleId, $saleDateTime, $totalPrice, $customerId, $staffId) {
    global $conn;

    $query = "UPDATE SALE SET SALEDATETIME = :1, TOTALPRICE = :2, CUSTOMERID = :3, STAFFID = :4 WHERE SALEID = :5";
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

    $query = "DELETE FROM SALE WHERE SALEID = :1";
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

//business rules
function getSalesByCustomer($customerId) {
    global $conn;

    $query = "SELECT SALEID, TO_CHAR(SALEDATETIME,'DD-MM-YY HH:MI:SS AM') AS SALEDATETIME, TOTALPRICE, CUSTOMERID, STAFFID, CUST.NAME AS CUSTOMER_NAME, STAFF.NAME AS STAFF_NAME, PAYMENTMETHOD FROM SALE LEFT JOIN USERS CUST ON SALE.CUSTOMERID = CUST.ID LEFT JOIN USERS STAFF ON SALE.STAFFID = STAFF.ID WHERE CUSTOMERID = :1 ORDER BY SALEDATETIME DESC";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $customerId);
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

function createSalesFromCart($customerId, $staffId, $paymentMethod = '') {
    global $conn;

    $cartItems = getCartItemsByCustomer($customerId);
    $totalPrice = 0;

    foreach ($cartItems as $item) {
        $totalPrice += $item['TOTAL_BY_ITEM'];
    }

    createSale($totalPrice, $customerId, $staffId, $paymentMethod);
    $saleId = getLatestSaleId();

    foreach ($cartItems as $item) {
        createSaleDetails($saleId, $item['ITEMID'], $item['QUANTITY'], $item['TOTAL_BY_ITEM']);
        updateStock($item['ITEMID'], -$item['QUANTITY']);
    }

    deleteAllCartItemsByCustomer($customerId);

    if ($saleId) {
        return ['status' => true, 'message' => 'Sale created from cart successfully.', 'sale_id' => $saleId];
    } else {
        return ['status' => false, 'message' => 'Failed to create sale from cart.'];
    }
}

function generateSalesReportByMonth($year) {
    global $conn;

        $query = "SELECT 
                        TO_CHAR(SALEDATETIME, 'YYYY-Mon') AS MONTH,
                        COUNT(*) AS TOTAL_SALES,
                        SUM(TOTALPRICE) AS TOTAL_REVENUE
                FROM SALE 
                WHERE EXTRACT(YEAR FROM SALEDATETIME) = :1 
                GROUP BY TO_CHAR(SALEDATETIME, 'YYYY-Mon')
                ORDER BY MONTH ASC";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $year);
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

function calculateTotalSalesThisWeek() {
    global $conn;

    $query = "SELECT SUM(TOTALPRICE) AS TOTAL_SALES_THIS_WEEK 
              FROM SALE 
              WHERE SALEDATETIME >= TRUNC(SYSDATE, 'IW')";
    $stmt = oci_parse($conn, $query);
    $result = oci_execute($stmt);

    $totalSales = 0;
    if ($result) {
        $row = oci_fetch_assoc($stmt);
        $totalSales = $row['TOTAL_SALES_THIS_WEEK'] ?? 0;
    }
    oci_free_statement($stmt);

    return $totalSales;
}
?>