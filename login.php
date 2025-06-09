<?php
// You can handle session start and login logic here (optional in this file)
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Login | sheroes</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>
        span {
    COLOR: HOTPINK;
}.navbar-nav .nav-link.active, .navbar-nav .nav-link.show {
    color: HOTPINK;
}
            :root {
                --primary-color:HOTPINK;
                --secondary-color:hotpink;
                --accent-color:HOTPINK;
                --light-color:HOTPINK;
                --dark-color:HOTPINK;
            }
            .nav-link {
    display: block;
    padding: var(--bs-nav-link-padding-y) var(--bs-nav-link-padding-x);
    font-size: var(--bs-nav-link-font-size);
    font-weight: var(--bs-nav-link-font-weight);
    color: HOTPINK;
    text-decoration: none;
    background: 0 0;
    border: 0;
    transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out;
}
            body {
                font-family: 'Poppins', sans-serif;
                background: linear-gradient 135deg,HOTPINK, url('https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?ixlib=rb-4.0.3') no-repeat center center fixed;
                background-size: cover;
                position: relative;
                min-height: 100vh;
                overflow: hidden;
                display: flex;
                align-items: center;
                justify-content: center;
            }
     
            /* Decorative shapes */
            .shape {
                position: absolute;
                border-radius: 50%;
                filter: blur(100px);
                opacity: 0.7;
                animation: float 6s ease-in-out infinite;
            }
            .shape.one {
                width: 300px;
                height: 300px;
                background: var(--accent-color);
                top: -50px;
                left: -50px;
            }
            .shape.two {
                width: 200px;
                height: 200px;
                background: var(--primary-color);
                bottom: -60px;
                right: -40px;
                animation-duration: 8s;
            }
            @keyframes float {
                0%, 100% {
                    transform: translateY(0) translateX(0);
                }
                50% {
                    transform: translateY(20px) translateX(20px);
                }
            }
                  .card {
    position: relative;
    border-radius: 20px;
    box-shadow: 0 20px 30px rgba(0,0,0,0.2);
    background: WHITE;
    backdrop-filter: blur(8px);
    border: none;
    width: 100%;
    max-width: 420px;
    z-index: 1;
    overflow: hidden;
}
            .login-header {
                text-align: center;
                padding: 2rem 1rem 1rem;
            }
            .login-header img {
                width: 80px;
                margin-bottom: 1rem;
                animation: rotate 10s infinite linear;
            }
            @keyframes rotate {
                0% {
                    transform: rotate(0deg);
                }
                100% {
                    transform: rotate(360deg);
                }
            }
            .login-header h3 {
                font-weight: 700;
                color: var(--dark-color);
                margin-bottom: 0.5rem;
                position: relative;
                display: inline-block;
            }
            .login-header h3::after {
                content: '';
                position: absolute;
                bottom: -8px;
                left: 50%;
                transform: translateX(-50%);
                width: 60px;
                height: 3px;
                background: var(--secondary-color);
                border-radius: 3px;
            }
            .form-control, .form-select {
                border: none;
                border-bottom: 2px solid HOTPINK;
                border-radius: 0;
                padding: 0.75rem 1rem;
                background: transparent;
                transition: border-color 0.3s;
            }
            .form-control:focus, .form-select:focus {
                border-bottom-color: var(--primary-color);
                box-shadow: none;
                background: rgba(220, 18, 183, 0.8);
            }
            .input-group-text {
                background: transparent;
                border: none;
                color: var(--primary-color);
            }
            .form-group {
                position: relative;
                margin-bottom: 1.5rem;
            }
            .form-group .toggle-pass {
                position: absolute;
                right: 1rem;
                top: 50%;
                transform: translateY(-50%);
                cursor: pointer;
                color: var(--secondary-color);
            }
            .btn-login {
                background-image: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
                border: none;
                border-radius: 50px;
                padding: 0.75rem;
                font-weight: 600;
                width: 100%;
                transition: transform 0.2s, box-shadow 0.2s;
            }
            .btn-login:hover {
                transform: translateY(-3px) scale(1.02);
                box-shadow: 0 8px 20px rgba(223, 18, 182, 0.3);
            }
            .text-danger {
    --bs-text-opacity: 1;
    color: hotpink;
}
            .login-link, .forgot-password a {
                color: var(--secondary-color);
                text-decoration: none;
                font-weight: 500;
            }
            .login-link:hover, .forgot-password a:hover {
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
        <div class="shape one"></div>
        <div class="shape two"></div>

        <?php include 'home-navbar.php'; ?>

        <div class="card p-4">
            <div class="login-header">
              
                <h3>Welcome Back</h3>
                <p class="text-muted">Sign in to your SHEROES account</p>
            </div>
            <form action="login-action.php" method="POST" class="px-3 pb-4">
                <!-- Role -->
                <div class="form-group">
                    <select name="role" class="form-select" required>
                        <option value="">-- Select Role --</option>
                        <option value="jobseeker">Job Seeker</option>
                        <option value="employer">Employer</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>

                <!-- Email -->
                <div class="form-group input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="Email Address" required>
                </div>

                <!-- Password -->
                <div class="form-group input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                    <span class="toggle-pass"><i class="fas fa-eye" id="togglePassword"></i></span>
                </div>

                <div class="forgot-password text-end mb-3">
                    <a href="forgot-password.php">Forgot Password?</a>
                </div>

                <button type="submit" class="btn btn-login">Login</button>

                <div class="text-center mt-3">
                    <p>Don't have an account? <a href="register.php" class="login-link">Register Now</a></p>
                </div>
            </form>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            const toggle = document.querySelector('#togglePassword');
            const pwd = document.querySelector('#password');
            toggle.addEventListener('click', () => {
                const type = pwd.getAttribute('type') === 'password' ? 'text' : 'password';
                pwd.setAttribute('type', type);
                toggle.classList.toggle('fa-eye-slash');
            });
        </script>
    </body>
</html>
