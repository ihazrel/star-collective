<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Admin Dashboard</h1>
    <p>Welcome to the admin dashboard.</p>

    <table border="1">
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Phone Number</th>
    </tr>

    <?php foreach ($users as $user): ?>
        <tr>
            <td><?= htmlspecialchars($user['NAME']) ?></td>
            <td><?= htmlspecialchars($user['EMAIL']) ?></td>
            <td><?= htmlspecialchars($user['PHONENUMBER']) ?></td>
        </tr>
    <?php endforeach; ?>

</table>
</body>
</html>