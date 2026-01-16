<?php
require_once __DIR__ . '/../functions/user-functions.php';

function ShowUser() {
    session_start();

    $users = getAllUsers();

    require __DIR__ . '/../../frontend/admin/dashboard.view.php';
}