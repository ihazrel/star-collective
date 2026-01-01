<?php
require_once '../config/db_connect.php';

function createVendor($companyName, $address, $dateJoined) {
    global $conn;

    $query = "INSERT INTO vendors (CompanyName, Address, DateJoined) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'sss', $companyName, $address, $dateJoined);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        return ['status' => true, 'message' => 'Vendor created successfully.'];
    } else {
        return ['status' => false, 'message' => 'Failed to create vendor.'];
    }
}

function getAllVendors() {
    global $conn;

    $query = "SELECT VendorID, CompanyName, Address, DateJoined FROM vendors";
    $result = mysqli_query($conn, $query);

    $vendors = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $vendors[] = $row;
    }

    return $vendors;
}

function getVendorById($vendorId) {
    global $conn;

    $query = "SELECT VendorID, CompanyName, Address, DateJoined FROM vendors WHERE VendorID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $vendorId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_assoc($result);
}

function editVendor($vendorId, $companyName, $address, $dateJoined) {
    global $conn;

    $query = "UPDATE vendors SET CompanyName = ?, Address = ?, DateJoined = ? WHERE VendorID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'sssi', $companyName, $address, $dateJoined, $vendorId);

    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        return ['status' => true, 'message' => 'Vendor updated successfully.'];
    } else {
        return ['status' => false, 'message' => 'Failed to update vendor.'];
    }
}

?>