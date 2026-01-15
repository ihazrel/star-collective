<?php
require_once  __DIR__ . '/../config/db_connect.php';

function createCloth($size, $isAdultOrChild, $sex) {
    global $conn;
    
    $query = "INSERT INTO cloths (Size, IsAdultOrChild, Sex) VALUES (:1, :2, :3)";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $size);
    oci_bind_by_name($stmt, ':2', $isAdultOrChild);
    oci_bind_by_name($stmt, ':3', $sex);

    $result = oci_execute($stmt);

    if ($result) {
        oci_free_statement($stmt);
        return ['status' => true, 'message' => 'Cloth created successfully.'];
    } else {
        $error = oci_error($stmt);
        oci_free_statement($stmt);
        return ['status' => false, 'message' => 'Failed to create cloth: ' . $error['message']];
    }
}

function getAllCloths() {
    global $conn;

    $query = "SELECT ClothID, Size, IsAdultOrChild, Sex FROM cloths";
    $stmt = oci_parse($conn, $query);
    $result = oci_execute($stmt);

    $cloths = [];
    if ($result) {
        while ($row = oci_fetch_assoc($stmt)) {
            $cloths[] = $row;
        }
    }
    oci_free_statement($stmt);

    return $cloths;
}

function getClothById($clothId) {
    global $conn;

    $query = "SELECT ClothID, Size, IsAdultOrChild, Sex FROM cloths WHERE ClothID = :1";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $clothId);
    $result = oci_execute($stmt);

    $row = null;
    if ($result) {
        $row = oci_fetch_assoc($stmt);
    }
    oci_free_statement($stmt);

    return $row;
}

function editCloth($clothId, $size, $isAdultOrChild, $sex) {
    global $conn;

    $query = "UPDATE cloths SET Size = :1, IsAdultOrChild = :2, Sex = :3 WHERE ClothID = :4";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $size);
    oci_bind_by_name($stmt, ':2', $isAdultOrChild);
    oci_bind_by_name($stmt, ':3', $sex);
    oci_bind_by_name($stmt, ':4', $clothId);
    $result = oci_execute($stmt);
    
    if ($result) {
        oci_free_statement($stmt);
        return ['status' => true, 'message' => 'Cloth updated successfully.'];
    } else {
        $error = oci_error($stmt);
        oci_free_statement($stmt);
        return ['status' => false, 'message' => 'Failed to update cloth: ' . $error['message']];
    }
}
?>