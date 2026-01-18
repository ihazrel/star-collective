<?php 
include('includes/header.php'); 

require_once __DIR__ . '/../backend/services/authentication.php';
require_once __DIR__ . '/../backend/services/auth-helper.php';

$IsAlert = false;

// If the user is already logged in, redirect to admin dashboard
if (UserIsLoggedIn() && UserIsAdmin()) {
    header("Location: /frontend/admin/index.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $IsAlert = false;

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $result = LoginUser($email, $password);

    if ($result['status'] && UserIsAdmin()) {
        header("Location: /frontend/admin/index.php");
        exit();

    } else if ($result['status'] && UserIsStaff()) {
        header("Location: /frontend/staff/index.php");
        exit();

    } else if ($result['status'] && UserIsCustomer()) {
        header("Location: /frontend/index.php");
        exit();
    } else {
        $IsAlert = true;
        $error_message = "Invalid email or password.";
    }
}

?>

<!-- Success and Error Banners -->
<?php if (isset($_SESSION['flash_message'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert" id="successBanner" style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; width: 90%; width: 85%;">
    <strong>Success!</strong> <?php echo $_SESSION['flash_message']; unset($_SESSION['flash_message']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<script>
    setTimeout(function() {
        const banner = document.getElementById('successBanner');
        if (banner) {
            banner.classList.remove('show');
            banner.addEventListener('transitionend', function() {
                banner.remove();
            });
        }
    }, 3000);
</script>
<?php endif; ?>
<?php if (isset($errorMessage)): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert" id="errorBanner" style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; width: 90%; width: 85%;">
    <strong>Error!</strong> <?php echo htmlspecialchars($errorMessage); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<script>
    setTimeout(function() {
        const banner = document.getElementById('errorBanner');
        if (banner) {
            banner.classList.remove('show');
            banner.addEventListener('transitionend', function() {
                banner.remove();
            });
        }
    }, 3000);
</script>
<?php endif; ?>

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
        background-color: rgba(36, 86, 51, 0.3); 
        padding: 40px;
        border-radius: 15px;
    }
</style>

<div class="container-fluid tm-content-container">
    <div class="login-container-wrapper">
        <div class="login-box tm-border-top tm-border-bottom">
            <div class="text-center mb-4">
                <h2 class="text-white">Login</h2>
            </div>
            
            <form action="#" method="POST" class="contact-form" autocomplete="off">
                
                <div class="input-group tm-mb-30">
                    <input name="email" type="email"
                        class="form-control rounded-0 border-top-0 border-end-0 border-start-0"
                        placeholder="Email" 
                        autocomplete="off"
                        required>
                </div>
                
                <div class="input-group tm-mb-30">
                    <input name="password" type="password"
                        class="form-control rounded-0 border-top-0 border-end-0 border-start-0"
                        placeholder="Password" 
                        autocomplete="new-password"
                        required>
                </div>
                
                <div class="input-group justify-content-center mt-4">
                    <input type="submit" class="btn btn-primary tm-btn-pad-2 w-100" value="Login">
                </div>
            </form>

            <div class="text-center mt-4">
                <p class="mb-2">Don't have an account? <a href="signup.php" class="highlight">Sign Up</a></p>
                <a href="index.php" class="tm-link-white small text-decoration-none">← Back to Website</a>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>