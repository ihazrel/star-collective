<?php 
include('includes/header.php'); 

// Start the session
session_start();

require_once __DIR__ . '/../backend/services/authentication.php';

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