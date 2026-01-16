<?php
session_start();

require_once __DIR__ . '/backend/functions/user-functions.php';
require_once __DIR__ . '/backend/services/authentication.php';

// Sample user data - replace with your database query
$users = getAllUsers();

// Handle login
if ($_POST['login'] ?? false) {
    $userId = (int)$_POST['user_id'];
    $user = getUserById($userId);

    session_regenerate_id(true);

        $_SESSION['user_id'] = $user['ID'];
        $_SESSION['user_name'] = $user['NAME'];
        $_SESSION['user_email'] = $user['EMAIL'];
        $_SESSION['user_phone'] = $user['PHONENUMBER'];
        $_SESSION['user_role'] = $user['ROLE'];
        $_SESSION['logged_in'] = true;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Master Login</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .user-card { border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 5px; }
        button { padding: 8px 15px; background: #007bff; color: white; border: none; border-radius: 3px; cursor: pointer; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h1>Select User to Login</h1>
    
    <table border="1" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f0f0f0;">
                <th style="padding: 10px; text-align: left;">Name</th>
                <th style="padding: 10px; text-align: left;">Email</th>
                <th style="padding: 10px; text-align: left;">Role</th>
                <th style="padding: 10px; text-align: left;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td style="padding: 10px;"><?= htmlspecialchars($user['NAME']) ?></td>
                    <td style="padding: 10px;"><?= htmlspecialchars($user['EMAIL']) ?></td>
                    <td style="padding: 10px;"><?= htmlspecialchars($user['ROLE']) ?></td>
                    <td style="padding: 10px;">
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="user_id" value="<?= $user['ID'] ?>">
                            <button type="submit" name="login" value="1">Login</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>