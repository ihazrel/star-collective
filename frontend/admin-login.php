<?php
// Start the session
session_start();

require_once '../backend/services/authentication.php';

$IsAlert = false;

// If the user is already logged in, redirect to admin dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin-dashboard.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $IsAlert = false;

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $result = LoginUser($email, $password);

    if ($result['status'] && $_SESSION['user_role'] === 'Staff') {
        header("Location: admin-dashboard.php");
        exit();
    } else {
        $IsAlert = true;
        $error_message = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Star Collective - Admin Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="src/assets/style/bootstrap.min.css">
  <link rel="stylesheet" href="src/assets/style/slick.css" type="text/css" />
  <link rel="stylesheet" href="src/assets/style/templatemo-style.css">

  <style>
    .login-container-wrapper {
        min-height: 70vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .login-box {
        max-width: 500px;
        width: 100%;
        background-color: rgba(0,0,0,0.7); 
        padding: 40px;
        border-radius: 15px;
    }
  </style>
</head>

<body>
  <?php
    if ($IsAlert) {
        echo '<div class="alert alert-danger text-center" role="alert">' . htmlspecialchars($error_message) . '</div>';
    }
  ?>

  <video autoplay muted loop id="bg-video">
    <source src="src/assets/video/gfp-astro-timelapse.mp4" type="video/mp4">
  </video>

  <div class="page-container">
    <div class="container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <div class="cd-slider-nav">
            <nav class="navbar navbar-expand-lg" id="tm-nav">
              <a class="navbar-brand" href="../index.html">Star Collective</a>
            </nav>
          </div>
        </div>
      </div>
    </div>

    <div class="container-fluid tm-content-container">
        
      <div class="login-container-wrapper">
          <div class="login-box tm-border-top tm-border-bottom">
              <div class="text-center mb-4">
                  <h2 class="text-white">Admin Login</h2>
              </div>
              
              <form action="#" method="POST" class="contact-form">
                  <div class="input-group tm-mb-30">
                      <input name="email" type="email"
                          class="form-control rounded-0 border-top-0 border-end-0 border-start-0"
                          placeholder="Email" required>
                  </div>
                  <div class="input-group tm-mb-30">
                      <input name="password" type="password"
                          class="form-control rounded-0 border-top-0 border-end-0 border-start-0"
                          placeholder="Password" required>
                  </div>
                  
                  <div class="input-group justify-content-center mt-4">
                      <input type="submit" class="btn btn-primary tm-btn-pad-2 w-100" value="Login">
                  </div>
              </form>

              <div class="text-center mt-3">
                <a href="../index.html" class="tm-link-white small text-decoration-none">← Back to Website</a>
              </div>

          </div>
      </div>

    </div>

    <div class="container-fluid">
      <footer class="row mx-auto tm-footer">
        <div class="col-md-6 px-0">
          Copyright 2026 Star Collective. All rights reserved.
        </div>
      </footer>
    </div>
  </div>

  <script src="src/js/jquery-3.5.1.min.js"></script>
  <script src="src/js/bootstrap.min.js"></script>
  <script src="src/js/slick.js"></script> 
  <script src="src/js/templatemo-script.js"></script>
</body>

</html>