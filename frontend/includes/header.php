<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Star Collective - Lifestyle & Apparel</title>
  <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/style/bootstrap.min.css">
  <link rel="stylesheet" href="assets/style/slick.css" type="text/css" />
  <link rel="stylesheet" href="assets/style/templatemo-style.css">
  <style>
    .nav-item.user-welcome .nav-link {
      color: #ffd700 !important;
      font-weight: 400;
      position: relative;
      padding-left: 30px;
    }
    
    .nav-item.user-welcome .nav-link::before {
      content: "✨";
      position: absolute;
      left: 8px;
      animation: twinkle 2s infinite;
    }
    
    @keyframes twinkle {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.5; }
    }
  </style>
</head>

<body>
  <video autoplay muted loop id="bg-video">
    <source src="../frontend/assets/video/gfp-astro-timelapse.mp4" type="video/mp4">
  </video>
  
  <div class="page-container">
    <div class="container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <div class="cd-slider-nav">
            <nav class="navbar navbar-expand-lg" id="tm-nav">
              <a class="navbar-brand" href="index.php">Star Collective</a>
              <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbar-supported-content" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
              </button>

              <div class="collapse navbar-collapse" id="navbar-supported-content">
                <ul class="navbar-nav mb-2 mb-lg-0">
                  <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                    <div class="circle"></div>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="collections.php">Collections</a>
                    <div class="circle"></div>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="about.php">About</a>
                    <div class="circle"></div>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="contact.php">Contact</a>
                    <div class="circle"></div>
                  </li>
                  <?php if (isset($_SESSION['user_name']) && !empty($_SESSION['user_name'])): ?>
                  <li class="nav-item user-welcome">
                    <a class="nav-link" href="invoice_list.php">Hello, <?php echo htmlspecialchars($_SESSION['user_name']); ?></a>
                    <div class="circle"></div>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                    <div class="circle"></div>
                  </li>
                  <?php else: ?>
                  <li class="nav-item">
                    <a class="nav-link" href="login.php">Login</a>
                    <div class="circle"></div>
                  </li>
                  <?php endif; ?>
                </ul>
              </div>
            </nav>
          </div>
        </div>
      </div>
    </div>