<?php include('includes/header.php'); ?>

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
            
            <form action="../backend/auth/login_process.php" method="POST" class="contact-form" autocomplete="off">
                
                <div class="input-group tm-mb-30">
                    <input name="username" type="text"
                        class="form-control rounded-0 border-top-0 border-end-0 border-start-0"
                        placeholder="Username" 
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