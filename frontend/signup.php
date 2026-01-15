<?php include('includes/header.php'); ?>

<style>
    .signup-container-wrapper {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 0;
    }
    .signup-box {
        max-width: 600px;
        width: 100%;
        background-color: rgba(36, 86, 51, 0.3); 
        padding: 40px;
        border-radius: 15px;
    }
</style>

<div class="container-fluid tm-content-container">
    <div class="signup-container-wrapper">
        <div class="signup-box tm-border-top tm-border-bottom">
            <div class="text-center mb-4">
                <h2 class="text-white">Create Account</h2>
                <p class="small">Join Star Collective today</p>
            </div>
            
            <form action="../backend/auth/signup_process.php" method="POST" class="contact-form">
                <div class="row">
                    <div class="col-md-12">
                        <div class="input-group tm-mb-30">
                            <input name="fullname" type="text"
                                class="form-control rounded-0 border-top-0 border-end-0 border-start-0"
                                placeholder="Full Name" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="input-group tm-mb-30">
                            <input name="username" type="text"
                                class="form-control rounded-0 border-top-0 border-end-0 border-start-0"
                                placeholder="Username" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="input-group tm-mb-30">
                            <input name="email" type="email"
                                class="form-control rounded-0 border-top-0 border-end-0 border-start-0"
                                placeholder="Email Address" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="input-group tm-mb-30">
                            <input name="password" type="password"
                                class="form-control rounded-0 border-top-0 border-end-0 border-start-0"
                                placeholder="Password" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="input-group tm-mb-30">
                            <input name="confirm_password" type="password"
                                class="form-control rounded-0 border-top-0 border-end-0 border-start-0"
                                placeholder="Confirm Password" required>
                        </div>
                    </div>
                </div>
                
                <div class="input-group justify-content-center mt-4">
                    <input type="submit" class="btn btn-primary tm-btn-pad-2 w-100" value="Register">
                </div>
            </form>

            <div class="text-center mt-4">
                <p class="mb-2">Already have an account? <a href="login.php" class="highlight">Login here</a></p>
                <a href="index.php" class="tm-link-white small text-decoration-none">← Back to Website</a>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>