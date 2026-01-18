<?php
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/auth-helper.php';
require_once __DIR__ . '/../functions/user-functions.php';

function registerUser($name, $email, $password, $role) {
    global $conn;

    $result = createUser($name, $email, null, $password, $role);

    if ($result['status']) {
        return ['status' => true, 'message' => 'Registration successful.'];
    } else {
        return ['status' => false, 'message' => 'Registration failed.'];
    }
}

function LoginUser($email, $password) {
    global $conn;

    $query = "SELECT * FROM USERS WHERE EMAIL = :email";

    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':email', $email);
    oci_execute($stmt);

    $user = oci_fetch_assoc($stmt);

    $authenticated = $user && password_verify($password, $user['PASSWORD']); // For demonstration; replace with password_verify in production);

    if ($authenticated) {
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['ID'];
        $_SESSION['user_name'] = $user['NAME'];
        $_SESSION['user_email'] = $user['EMAIL'];
        $_SESSION['user_phone'] = $user['PHONENUMBER'];
        $_SESSION['user_role'] = $user['ROLE'];
        $_SESSION['logged_in'] = true;

        return ['status' => true, 'message' => 'Login successful.', 'user' => $user];
    } else {
        return ['status' => false, 'message' => 'Invalid username or password.'];
    }
}

function LogoutUser() {
    session_start();
    $_SESSION = [];
    session_destroy();
}
?>