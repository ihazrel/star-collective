<?php
require_once __DIR__ . '/../config/db_connect.php';

function createStaff($position, $salary, $dateHired, $managedBy) {
    global $conn;
    
    $query = "INSERT INTO staff (Position, Salary, DateHired, ManagedBy) VALUES (:1, :2, :3, :4)";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $position);
    oci_bind_by_name($stmt, ':2', $salary);
    oci_bind_by_name($stmt, ':3', $dateHired);
    oci_bind_by_name($stmt, ':4', $managedBy);

    $result = oci_execute($stmt);

    if ($result) {
        oci_free_statement($stmt);
        return ['status' => true, 'message' => 'Staff created successfully.'];
    } else {
        $error = oci_error($stmt);
        oci_free_statement($stmt);
        return ['status' => false, 'message' => 'Failed to create staff: ' . $error['message']];
    }
}

function getAllStaff() {
    global $conn;

    $query = "SELECT StaffID, Position, Salary, DateHired, ManagedBy FROM staff";
    $stmt = oci_parse($conn, $query);
    $result = oci_execute($stmt);

    $staffList = [];
    if ($result) {
        while ($row = oci_fetch_assoc($stmt)) {
            $staffList[] = $row;
        }
    }
    oci_free_statement($stmt);

    return $staffList;
}

function getStaffById($staffId) {
    global $conn;

    $query = "SELECT StaffID, Position, Salary, DateHired, ManagedBy FROM staff WHERE StaffID = :1";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $staffId);
    $result = oci_execute($stmt);

    $row = null;
    if ($result) {
        $row = oci_fetch_assoc($stmt);
    }
    oci_free_statement($stmt);

    return $row;
}

function editStaff($staffId, $position, $salary, $dateHired, $managedBy) {
    global $conn;

    $query = "UPDATE staff SET Position = :1, Salary = :2, DateHired = :3, ManagedBy = :4 WHERE StaffID = :5";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':1', $position);
    oci_bind_by_name($stmt, ':2', $salary);
    oci_bind_by_name($stmt, ':3', $dateHired);
    oci_bind_by_name($stmt, ':4', $managedBy);
    oci_bind_by_name($stmt, ':5', $staffId);

    $result = oci_execute($stmt);

    if ($result) {
        oci_free_statement($stmt);
        return ['status' => true, 'message' => 'Staff updated successfully.'];
    } else {
        $error = oci_error($stmt);
        oci_free_statement($stmt);
        return ['status' => false, 'message' => 'Failed to update staff: ' . $error['message']];
    }
}
?>