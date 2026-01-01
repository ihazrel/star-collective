<?php
require_once '../config/db_connect.php';

function createUser($name, $email, $phone, $password) {
    global $conn;
    
    $password = password_hash($password, PASSWORD_BCRYPT); // Hash the password

    $query = "INSERT INTO users (Name, Email, PhoneNumber, Password) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ssss', $name, $email, $phone, $password);

    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        return ['status' => true, 'message' => 'User created successfully.'];
    } else {
        return ['status' => false, 'message' => 'Failed to create user.'];
    }
}

function getAllUsers() {
    global $conn;

    $query = "SELECT UserID, Name, Email, PhoneNumber FROM users";
    $result = mysqli_query($conn, $query);

    $users = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }

    return $users;
}

function getUserById($userId) {
    global $conn;

    $query = "SELECT UserID, Name, Email, PhoneNumber FROM users WHERE UserID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_assoc($result);
}

function editUser($userId, $name, $email, $phone) {
    global $conn;

    $query = "UPDATE users SET Name = ?, Email = ?, PhoneNumber = ? WHERE UserID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'sssi', $name, $email, $phone, $userId);

    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        return ['status' => true, 'message' => 'User updated successfully.'];
    } else {
        return ['status' => false, 'message' => 'Failed to update user.'];
    }
}

function editPassword($userId, $newPassword) {
    global $conn;

    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT); // Hash the new password

    $query = "UPDATE users SET Password = ? WHERE UserID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'si', $hashedPassword, $userId);

    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        return ['status' => true, 'message' => 'Password updated successfully.'];
    } else {
        return ['status' => false, 'message' => 'Failed to update password.'];
    }
}

function deleteUser($userId) {
    global $conn;

    $query = "DELETE FROM users WHERE UserID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $userId);

    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        return ['status' => true, 'message' => 'User deleted successfully.'];
    } else {
        return ['status' => false, 'message' => 'Failed to delete user.'];
    }
}
?>