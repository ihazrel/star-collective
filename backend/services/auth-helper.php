<?php

function UserIsLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function hasRole($role) {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
}

function UserIsStaff() {
    return hasRole('Staff');
}

function UserIsAdmin() {
    return hasRole('Admin');
}

function UserIsCustomer() {
    return hasRole('Customer');
}

function UserIsVendor() {
    return hasRole('Vendor');
}

function RequireLogin() {
    if (!UserIsLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function RequireRole($role) {
    RequireLogin();
    if (!hasRole($role)) {
        header('Location: unauthorized.php');
        exit;
    }
}

function RequireStaff() {
    RequireRole('Staff');
}

function RequireVendor() {
    RequireRole('Vendor');
}
?>