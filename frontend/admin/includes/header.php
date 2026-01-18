<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Star Collective</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse p-3">
            <div class="position-sticky">
                <h4 class="text-accent mb-4 px-3 fw-bold">STAR COLLECTIVE</h4>
                <ul class="nav flex-column gap-1">
                    <li class="nav-item">
                        <a class="nav-link active rounded p-2" href="#overview">Overview</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded p-2" href="#users">User Management</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded p-2" href="#inventory">Inventory Control</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded p-2" href="#procurement">Procurement</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded p-2" href="#sales">Sales Operations</a>
                    </li>
                    <hr class="text-secondary">
                    <li class="nav-item mt-2">
                        <button class="btn btn-outline-danger w-100 btn-sm" onclick="logout()">Logout System</button>
                    </li>
                </ul>
            </div>
        </nav>

        <script>
            function logout() {
                fetch('logout.php', {
                    method: 'POST'
                }).then(response => {
                    if (response.ok) {
                        window.location.href = '../login.php';
                    }
                });
            }
        </script>