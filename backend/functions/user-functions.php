<?php
require_once __DIR__ . '/../config/db_connect.php';

function createUser($name, $email, $phone, $password, $role) {
    global $conn;
    
    // Check if email already exists
    $existingUser = getUserByEmail($email);
    if ($existingUser) {
        return ['status' => false, 'message' => 'Email already exists.'];
    }
    
    $password = password_hash($password, PASSWORD_BCRYPT); // Hash the password

    

    $query = "INSERT INTO users (NAME, EMAIL, PHONENUMBER, PASSWORD, ROLE) VALUES (:name, :email, :phone, :password, :role)";
    $stmt = oci_parse($conn, $query);
    
    oci_bind_by_name($stmt, ':name', $name);
    oci_bind_by_name($stmt, ':email', $email);
    oci_bind_by_name($stmt, ':phone', $phone);
    oci_bind_by_name($stmt, ':password', $password);
    oci_bind_by_name($stmt, ':role', $role);
    $result = oci_execute($stmt);

    // If role is customer, also insert into customer table
    if (strtolower($role) === 'customer') {
        $customerQuery = "INSERT INTO customer (USER_ID) VALUES (:userId)";
        $customerStmt = oci_parse($conn, $customerQuery);
        oci_bind_by_name($customerStmt, ':userId', $userId);
        oci_execute($customerStmt);
    }

    if ($result) {
        return ['status' => true, 'message' => 'User created successfully.'];
    } else {
        return ['status' => false, 'message' => 'Failed to create user.'];
    }
}

function getAllUsers($role = null) {
    global $conn;

    $query = "SELECT ID, NAME, EMAIL, PHONENUMBER, ROLE FROM users ";
    if ($role !== null) {
        $query .= "WHERE LOWER(ROLE) = :role";
    }
    $stmt = oci_parse($conn, $query);

    if ($role !== null) {
        oci_bind_by_name($stmt, ':role', $role);
    }
    oci_execute($stmt);

    $users = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $users[] = $row;
    }
    oci_free_statement($stmt);

    return $users;
}

function getUserById($userId) {
    global $conn;

    $query = "SELECT ID, NAME, EMAIL, PHONENUMBER, ROLE FROM users WHERE ID = :userId";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':userId', $userId);
    oci_execute($stmt);

    return oci_fetch_assoc($stmt);
}

function getUserByEmail($email) {
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

function getAllStaffs() {

    $admins = getAllUsers('admin');
    $staffs = getAllUsers('staff');

    $users = array_merge($admins, $staffs);

    return $users;
} 
?>