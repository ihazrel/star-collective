<?php
require_once '../config/db_connect.php';

function registerUser($email, $password) {
    global $conn;

    require_once '../functions/user-functions.php';

    $existingUser = getUserByEmail($email);

    if ($existingUser) {
        return ['status' => false, 'message' => 'Email already registered.'];
    }

    $result = createUser(null, $email, null, $password);

    if ($result['status']) {
        return ['status' => true, 'message' => 'Registration successful.'];
    } else {
        return ['status' => false, 'message' => 'Registration failed.'];
    }
}

function loginUser($email, $password) {
    global $conn;

    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    $authenticated = $user && password_verify($password, $user['password']);

    if ($authenticated) {
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['UserID'];
        $_SESSION['user_name'] = $user['Name'];
        $_SESSION['user_email'] = $user['Email'];
        $_SESSION['user_phone'] = $user['PhoneNumber'];
        $_SESSION['user_role'] = $user['Role'];
        $_SESSION['logged_in'] = true;

        return ['status' => true, 'message' => 'Login successful.', 'user' => $user];
    } else {
        return ['status' => false, 'message' => 'Invalid username or password.'];
    }
}
?>