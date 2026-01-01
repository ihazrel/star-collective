<?php

function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function hasRole($role) {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
}

function isStaff() {
    return hasRole('staff');
}

function isCustomer() {
    return hasRole('customer');
}

function isVendor() {
    return hasRole('vendor');
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function requireRole($role) {
    requireLogin();
    if (!hasRole($role)) {
        header('Location: unauthorized.php');
        exit;
    }
}

function requireStaff() {
    requireRole('staff');
}

function requireVendor() {
    requireRole('vendor');
}
?>