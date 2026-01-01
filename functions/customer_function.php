<?php
require_once '../config/db_connect.php';

function createCustomer($membershipNumber, $dateJoined) {
    global $conn;
    
    $query = "INSERT INTO customers (MembershipNumber, DateJoined) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ss', $membershipNumber, $dateJoined);

    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        return ['status' => true, 'message' => 'Customer created successfully.'];
    } else {
        return ['status' => false, 'message' => 'Failed to create customer.'];
    }
}

function getAllCustomers() {
    global $conn;

    $query = "SELECT CustomerID, MembershipNumber, DateJoined FROM customers";
    $result = mysqli_query($conn, $query);

    $customers = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $customers[] = $row;
    }

    return $customers;
}

function getCustomerById($customerId) {
    global $conn;

    $query = "SELECT CustomerID, MembershipNumber, DateJoined FROM customers WHERE CustomerID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $customerId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_assoc($result);
}

function editCustomer($customerId, $membershipNumber, $dateJoined) {
    global $conn;

    $query = "UPDATE customers SET MembershipNumber = ?, DateJoined = ? WHERE CustomerID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ssi', $membershipNumber, $dateJoined, $customerId);

    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        return ['status' => true, 'message' => 'Customer updated successfully.'];
    } else {
        return ['status' => false, 'message' => 'Failed to update customer.'];
    }
}
?>