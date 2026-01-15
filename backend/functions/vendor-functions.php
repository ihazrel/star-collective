<?php
require_once '../config/db_connect.php';

function createVendor($companyName, $address, $dateJoined) {
    global $conn;

    $query = "INSERT INTO vendors (CompanyName, Address, DateJoined) VALUES (:1, :2, :3)";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $companyName);
    oci_bind_by_name($stmt, ':2', $address);
    oci_bind_by_name($stmt, ':3', $dateJoined);
    $result = oci_execute($stmt);

    if ($result) {
        oci_free_statement($stmt);
        return ['status' => true, 'message' => 'Vendor created successfully.'];
    } else {
        $error = oci_error($stmt);
        oci_free_statement($stmt);
        return ['status' => false, 'message' => 'Failed to create vendor: ' . $error['message']];
    }
}

function getAllVendors() {
    global $conn;

    $query = "SELECT VendorID, CompanyName, Address, DateJoined FROM vendors";
    $stmt = oci_parse($conn, $query);
    $result = oci_execute($stmt);

    $vendors = [];
    if ($result) {
        while ($row = oci_fetch_assoc($stmt)) {
            $vendors[] = $row;
        }
    }
    oci_free_statement($stmt);

    return $vendors;
}

function getVendorById($vendorId) {
    global $conn;

    $query = "SELECT VendorID, CompanyName, Address, DateJoined FROM vendors WHERE VendorID = :1";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $vendorId);
    $result = oci_execute($stmt);

    $row = null;
    if ($result) {
        $row = oci_fetch_assoc($stmt);
    }
    oci_free_statement($stmt);

    return $row;
}

function editVendor($vendorId, $companyName, $address, $dateJoined) {
    global $conn;

    $query = "UPDATE vendors SET CompanyName = :1, Address = :2, DateJoined = :3 WHERE VendorID = :4";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $companyName);
    oci_bind_by_name($stmt, ':2', $address);
    oci_bind_by_name($stmt, ':3', $dateJoined);
    oci_bind_by_name($stmt, ':4', $vendorId);

    $result = oci_execute($stmt);

    if ($result) {
        oci_free_statement($stmt);
        return ['status' => true, 'message' => 'Vendor updated successfully.'];
    } else {
        $error = oci_error($stmt);
        oci_free_statement($stmt);
        return ['status' => false, 'message' => 'Failed to update vendor: ' . $error['message']];
    }
}

?>