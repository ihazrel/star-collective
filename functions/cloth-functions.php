<?php
require_once '../config/db_connect.php';

function createCloth($size, $isAdultOrChild, $sex) {
    global $conn;
    
    $query = "INSERT INTO cloths (Size, IsAdultOrChild, Sex) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ssi', $size, $isAdultOrChild, $sex);

    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        return ['status' => true, 'message' => 'Cloth created successfully.'];
    } else {
        return ['status' => false, 'message' => 'Failed to create cloth.'];
    }
}

function getAllCloths() {
    global $conn;

    $query = "SELECT ClothID, Size, IsAdultOrChild, Sex FROM cloths";
    $result = mysqli_query($conn, $query);

    $cloths = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $cloths[] = $row;
    }

    return $cloths;
}

function getClothById($clothId) {
    global $conn;

    $query = "SELECT ClothID, Size, IsAdultOrChild, Sex FROM cloths WHERE ClothID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $clothId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_assoc($result);
}

function editCloth($clothId, $size, $isAdultOrChild, $sex) {
    global $conn;

    $query = "UPDATE cloths SET Size = ?, IsAdultOrChild = ?, Sex = ? WHERE ClothID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ssii', $size, $isAdultOrChild, $sex, $clothId);
    $result = mysqli_stmt_execute($stmt);
    
    if ($result) {
        return ['status' => true, 'message' => 'Cloth updated successfully.'];
    } else {
        return ['status' => false, 'message' => 'Failed to update cloth.'];
    }
}
?>