<?php
require_once '../config/db_connect.php';

function createStaff($position, $salary, $dateHired, $managedBy) {
    global $conn;
    
    $query = "INSERT INTO staff (Position, Salary, DateHired, ManagedBy) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'sdis', $position, $salary, $dateHired, $managedBy);

    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        return ['status' => true, 'message' => 'Staff created successfully.'];
    } else {
        return ['status' => false, 'message' => 'Failed to create staff.'];
    }
}

function getAllStaff() {
    global $conn;

    $query = "SELECT StaffID, Position, Salary, DateHired, ManagedBy FROM staff";
    $result = mysqli_query($conn, $query);

    $staffList = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $staffList[] = $row;
    }

    return $staffList;
}

function getStaffById($staffId) {
    global $conn;

    $query = "SELECT StaffID, Position, Salary, DateHired, ManagedBy FROM staff WHERE StaffID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $staffId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_assoc($result);
}

function editStaff($staffId, $position, $salary, $dateHired, $managedBy) {
    global $conn;

    $query = "UPDATE staff SET Position = ?, Salary = ?, DateHired = ?, ManagedBy = ? WHERE StaffID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'sdisi', $position, $salary, $dateHired, $managedBy, $staffId);

    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        return ['status' => true, 'message' => 'Staff updated successfully.'];
    } else {
        return ['status' => false, 'message' => 'Failed to update staff.'];
    }
}
?>