<?php
require_once __DIR__ . '/../config/db_connect.php';

function createUser($name, $email, $phone, $password) {
    global $conn;
    
    $password = password_hash($password, PASSWORD_BCRYPT); // Hash the password

    $query = "INSERT INTO users (NAME, EMAIL, PHONENUMBER, Password) VALUES (:name, :email, :phone, :password)";
    $stmt = oci_parse($conn, $query);
    
    oci_bind_by_name($stmt, ':name', $name);
    oci_bind_by_name($stmt, ':email', $email);
    oci_bind_by_name($stmt, ':phone', $phone);
    oci_bind_by_name($stmt, ':password', $password);

    $result = oci_execute($stmt);

    if ($result) {
        return ['status' => true, 'message' => 'User created successfully.'];
    } else {
        return ['status' => false, 'message' => 'Failed to create user.'];
    }
}

function getAllUsers() {
    global $conn;

    $query = "SELECT ID, NAME, EMAIL, PHONENUMBER FROM users";
    $result = oci_parse($conn, $query);
    oci_execute($result);

    $users = [];
    while ($row = oci_fetch_assoc($result)) {
        $users[] = $row;
    }
    oci_free_statement($result);

    return $users;
}

function getUserById($userId) {
    global $conn;

    $query = "SELECT ID, NAME, EMAIL, PHONENUMBER FROM users WHERE ID = :userId";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':userId', $userId);
    oci_execute($stmt);

    return oci_fetch_assoc($stmt);
}

function getUserByEMAIL($email) {
    global $conn;

    $query = "SELECT * FROM users WHERE EMAIL = :email";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':email', $email);
    oci_execute($stmt);

    return oci_fetch_assoc($stmt);
}

function editUser($userId, $name, $email, $phone) {
    global $conn;

    $query = "UPDATE users SET NAME = :name, EMAIL = :email, PHONENUMBER = :phone WHERE ID = :userId";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':name', $name);
    oci_bind_by_name($stmt, ':email', $email);
    oci_bind_by_name($stmt, ':phone', $phone);
    oci_bind_by_name($stmt, ':userId', $userId);

    $result = oci_execute($stmt);

    if ($result) {
        return ['status' => true, 'message' => 'User updated successfully.'];
    } else {
        return ['status' => false, 'message' => 'Failed to update user.'];
    }
}

function EditPassword($userId, $newPassword) {
    global $conn;

    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT); // Hash the new password

    $query = "UPDATE USERS SET Password = :password WHERE ID = :userId";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':password', $hashedPassword);
    oci_bind_by_name($stmt, ':userId', $userId);

    $result = oci_execute($stmt);

    if ($result) {
        return ['status' => true, 'message' => 'Password updated successfully.'];
    } else {
        return ['status' => false, 'message' => 'Failed to update password.'];
    }
}

function deleteUser($userId) {
    global $conn;

    $query = "DELETE FROM users WHERE ID = :userId";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':userId', $userId);

    $result = oci_execute($stmt);

    if ($result) {
        return ['status' => true, 'message' => 'User deleted successfully.'];
    } else {
        return ['status' => false, 'message' => 'Failed to delete user.'];
    }
}
?>