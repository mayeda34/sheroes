<?php
// You can add database connection and form handling logic here if needed
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Register | sheroes</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>
            :root {
                --primary-color:rgba(223, 48, 188, 0.6);
                --secondary-color:rgba(254, 155, 216, 0.83);
                --accent-color: #fd79a8;
                --light-color:rgb(229, 11, 171);
                --dark-color:rgb(173, 28, 134);
                --gradient-primary: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            }

            body {
                font-family: 'Poppins', sans-serif;
                background: url('https://images.unsplash.com/photo-1579389083078-4e7018379f7e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80') no-repeat center center fixed;
                background-size: cover;
                position: relative;
                min-height: 100vh;
                display: flex;
                align-items: center;
            }

            body::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(45, 52, 54, 0.85);
                z-index: -1;
            }

            .register-container {
                max-width: 1200px;
                margin: 0 auto;
            }

            .register-card {
                border-radius: 20px;
                box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border: none;
                overflow: hidden;
                transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            }

            .register-card:hover {
                transform: translateY(-10px);
                box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
            }

            .register-header {
                background: var(--gradient-primary);
                color: white;
                padding: 2rem;
                text-align: center;
                position: relative;
                overflow: hidden;
            }

            .register-header::before {
                content: '';
                position: absolute;
                top: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 70%);
                transform: rotate(30deg);
                animation: shine 8s infinite linear;
            }

            @keyframes shine {
                0% {
                    transform: rotate(30deg) translate(-30%, -30%);
                }
                100% {
                    transform: rotate(30deg) translate(30%, 30%);
                }
            }

            .register-header h3 {
                font-weight: 700;
                font-size: 2rem;
                position: relative;
                margin-bottom: 0.5rem;
            }

            .register-header p {
                font-size: 1rem;
                opacity: 0.9;
                position: relative;
            }

            .nav-tabs {
                border: none;
                margin: 0;
                position: relative;
                z-index: 1;
            }

            .nav-tabs .nav-link {
                border: none;
                padding: 1.2rem 1.5rem;
                font-weight: 600;
                color: var(--dark-color);
                background: transparent;
                position: relative;
                margin: 0;
                border-radius: 0;
                transition: all 0.3s ease;
            }

            .nav-tabs .nav-link::before {
                content: '';
                position: absolute;
                bottom: 0;
                left: 50%;
                transform: translateX(-50%);
                width: 0;
                height: 3px;
                background: var(--primary-color);
                transition: all 0.3s ease;
            }

            .nav-tabs .nav-link:hover {
                color: var(--primary-color);
                background: rgba(108, 92, 231, 0.05);
            }

            .nav-tabs .nav-link.active {
                color: var(--primary-color);
                background: transparent;
            }

            .nav-tabs .nav-link.active::before {
                width: 100%;
            }

            .tab-content {
                padding: 2.5rem;
            }

            .form-control {
                border: 2px solid #dfe6e9;
                padding: 1rem;
                border-radius: 12px;
                transition: all 0.3s ease;
                background-color: white;
                font-size: 0.95rem;
            }

            .form-control:focus {
                border-color: var(--primary-color);
                box-shadow: 0 0 0 0.25rem rgba(108, 92, 231, 0.25);
            }

            .form-label {
                font-weight: 600;
                color: var(--dark-color);
                margin-bottom: 0.5rem;
                font-size: 0.9rem;
                display: flex;
                align-items: center;
            }

            .form-icon {
                color: var(--primary-color);
                margin-right: 8px;
                font-size: 1rem;
            }

            .btn-register {
                background: var(--gradient-primary);
                border: none;
                padding: 1rem 2rem;
                font-weight: 600;
                letter-spacing: 0.5px;
                border-radius: 12px;
                transition: all 0.3s ease;
                box-shadow: 0 10px 20px rgba(108, 92, 231, 0.3);
                text-transform: uppercase;
                font-size: 0.9rem;
                position: relative;
                overflow: hidden;
            }

            .btn-register:hover {
                transform: translateY(-3px);
                box-shadow: 0 15px 30px rgba(108, 92, 231, 0.4);
            }

            .btn-register::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
                transition: 0.5s;
            }

            .btn-register:hover::before {
                left: 100%;
            }

            .feature-badge {
                display: inline-flex;
                align-items: center;
                background: rgba(108, 92, 231, 0.1);
                color: var(--primary-color);
                padding: 0.5rem 1rem;
                border-radius: 50px;
                font-size: 0.8rem;
                margin: 0.5rem;
                font-weight: 500;
                transition: all 0.3s ease;
            }

            .feature-badge:hover {
                transform: translateY(-3px);
                box-shadow: 0 5px 15px rgba(108, 92, 231, 0.1);
            }

            .feature-badge i {
                margin-right: 5px;
            }

            .login-link {
                color: var(--primary-color);
                font-weight: 600;
                text-decoration: none;
                transition: all 0.3s ease;
                position: relative;
            }

            .login-link::after {
                content: '';
                position: absolute;
                bottom: -2px;
                left: 0;
                width: 0;
                height: 2px;
                background: var(--primary-color);
                transition: width 0.3s ease;
            }

            .login-link:hover {
                color: var(--secondary-color);
                text-decoration: none;
            }

            .login-link:hover::after {
                width: 100%;
            }

            .floating-label {
                position: relative;
                margin-bottom: 1.5rem;
            }

            .floating-label label {
                position: absolute;
                top: -10px;
                left: 15px;
                background: white;
                padding: 0 8px;
                font-size: 0.8rem;
                color: var(--primary-color);
                font-weight: 600;
                z-index: 1;
            }

            .tab-pane {
                animation: fadeIn 0.5s ease-out;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* Custom checkbox */
            .form-check-input:checked {
                background-color: var(--primary-color);
                border-color: var(--primary-color);
            }

            /* Responsive adjustments */
            @media (max-width: 768px) {
                .register-card {
                    border-radius: 0;
                }

                .nav-tabs .nav-link {
                    padding: 1rem;
                    font-size: 0.9rem;
                }

                .tab-content {
                    padding: 1.5rem;
                }
            }
        </style>
    </head>
    <body>
        <div class="register-container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="register-card">
                        <div class="register-header">
                            <h3><i class="fas fa-user-plus me-2"></i>Join SHEROES</h3>
                            <p>Create your account and unlock amazing opportunities</p>
                        </div>

                        <ul class="nav nav-tabs nav-fill" id="registerTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="jobSeeker-tab" data-bs-toggle="tab" data-bs-target="#jobSeeker" type="button" role="tab">
                                    <i class="fas fa-user-tie me-2"></i>Job Seeker
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="employer-tab" data-bs-toggle="tab" data-bs-target="#employer" type="button" role="tab">
                                    <i class="fas fa-building me-2"></i>Employer
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="admin-tab" data-bs-toggle="tab" data-bs-target="#admin" type="button" role="tab">
                                    <i class="fas fa-user-shield me-2"></i>Admin
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="registerTabContent">

                            <!-- Job Seeker Form -->
                            <div class="tab-pane fade show active" id="jobSeeker" role="tabpanel">
                                <form action="jobseeker-register-action.php" method="POST" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="floating-label mb-4">
                                                <label for="js-name"><i class="fas fa-user form-icon"></i>Full Name</label>
                                                <input type="text" class="form-control" id="js-name" name="name" placeholder="John Doe" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="floating-label mb-4">
                                                <label for="js-email"><i class="fas fa-envelope form-icon"></i>Email</label>
                                                <input type="email" class="form-control" id="js-email" name="email" placeholder="john@example.com" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="floating-label mb-4">
                                                <label for="js-password"><i class="fas fa-lock form-icon"></i>Password</label>
                                                <input type="password" class="form-control" id="js-password" name="password" placeholder="••••••••" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="floating-label mb-4">
                                                <label for="js-phone"><i class="fas fa-phone form-icon"></i>Phone</label>
                                                <input type="tel" class="form-control" id="js-phone" name="phone" placeholder="+92 234 567 890" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="floating-label mb-4">
                                        <label for="js-location"><i class="fas fa-map-marker-alt form-icon"></i>Location</label>
                                        <input type="text" class="form-control" id="js-location" name="location" placeholder="Lahore, PK" required>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="floating-label mb-4">
                                                <label for="js-education"><i class="fas fa-graduation-cap form-icon"></i>Education</label>
                                                <textarea class="form-control" id="js-education" name="education" placeholder="Bachelor's in Computer Science" rows="2"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="floating-label mb-4">
                                                <label for="js-experience"><i class="fas fa-briefcase form-icon"></i>Experience</label>
                                                <textarea class="form-control" id="js-experience" name="experience" placeholder="2 years as Web Developer" rows="2"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="js-resume" class="form-label"><i class="fas fa-file-pdf form-icon"></i>Upload Resume (PDF/DOC)</label>
                                        <input type="file" class="form-control" id="js-resume" name="resume" accept=".pdf,.doc,.docx" required>
                                    </div>

                                    <div class="form-check mb-4">
                                        <input class="form-check-input" type="checkbox" id="js-terms" required>
                                        <label class="form-check-label" for="js-terms">
                                            I agree to the <a href="#" class="login-link">Terms of Service</a> and <a href="#" class="login-link">Privacy Policy</a>
                                        </label>
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-register">
                                            <i class="fas fa-rocket me-2"></i> Launch Your Career
                                        </button>
                                    </div>

                                    <div class="text-center mt-4">
                                        <div class="d-flex flex-wrap justify-content-center">
                                            <span class="feature-badge"><i class="fas fa-check-circle"></i> Personalized Job Matches</span>
                                            <span class="feature-badge"><i class="fas fa-check-circle"></i> Career Resources</span>
                                            <span class="feature-badge"><i class="fas fa-check-circle"></i> Networking</span>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Employer Form -->
                            <div class="tab-pane fade" id="employer" role="tabpanel">
                                <form action="employer-register-action.php" method="POST">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="floating-label mb-4">
                                                <label for="emp-company"><i class="fas fa-building form-icon"></i>Company Name</label>
                                                <input type="text" class="form-control" id="emp-company" name="company_name" placeholder="Tech Solutions Inc." required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="floating-label mb-4">
                                                <label for="emp-contact"><i class="fas fa-user form-icon"></i>Contact Person</label>
                                                <input type="text" class="form-control" id="emp-contact" name="contact_person" placeholder="Jane Smith" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="floating-label mb-4">
                                                <label for="emp-email"><i class="fas fa-envelope form-icon"></i>Email</label>
                                                <input type="email" class="form-control" id="emp-email" name="email" placeholder="contact@company.com" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="floating-label mb-4">
                                                <label for="emp-password"><i class="fas fa-lock form-icon"></i>Password</label>
                                                <input type="password" class="form-control" id="emp-password" name="password" placeholder="••••••••" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="floating-label mb-4">
                                                <label for="emp-phone"><i class="fas fa-phone form-icon"></i>Phone</label>
                                                <input type="tel" class="form-control" id="emp-phone" name="phone" placeholder="+92 234 567 890" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="floating-label mb-4">
                                                <label for="emp-location"><i class="fas fa-map-marker-alt form-icon"></i>Location</label>
                                                <input type="text" class="form-control" id="emp-location" name="location" placeholder="Lahore, PK" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-check mb-4">
                                        <input class="form-check-input" type="checkbox" id="emp-terms" required>
                                        <label class="form-check-label" for="emp-terms">
                                            I agree to the <a href="#" class="login-link">Terms of Service</a> and <a href="#" class="login-link">Privacy Policy</a>
                                        </label>
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-register">
                                            <i class="fas fa-briefcase me-2"></i> Find Top Talent
                                        </button>
                                    </div>

                                    <div class="text-center mt-4">
                                        <div class="d-flex flex-wrap justify-content-center">
                                            <span class="feature-badge"><i class="fas fa-check-circle"></i> Access Top Candidates</span>
                                            <span class="feature-badge"><i class="fas fa-check-circle"></i> Advanced Hiring Tools</span>
                                            <span class="feature-badge"><i class="fas fa-check-circle"></i> Employer Branding</span>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Admin Form -->
                            <div class="tab-pane fade" id="admin" role="tabpanel">
                                <form action="admin-register-action.php" method="POST">
                                    <div class="floating-label mb-4">
                                        <label for="admin-name"><i class="fas fa-user-shield form-icon"></i>Admin Name</label>
                                        <input type="text" class="form-control" id="admin-name" name="name" placeholder="Admin Name" required>
                                    </div>

                                    <div class="floating-label mb-4">
                                        <label for="admin-email"><i class="fas fa-envelope form-icon"></i>Email</label>
                                        <input type="email" class="form-control" id="admin-email" name="email" placeholder="admin@sheroes.com" required>
                                    </div>

                                    <div class="floating-label mb-4">
                                        <label for="admin-password"><i class="fas fa-lock form-icon"></i>Password</label>
                                        <input type="password" class="form-control" id="admin-password" name="password" placeholder="••••••••" required>
                                    </div>

                                    <div class="form-check mb-4">
                                        <input class="form-check-input" type="checkbox" id="admin-terms" required>
                                        <label class="form-check-label" for="admin-terms">
                                            I agree to the <a href="#" class="login-link">Terms of Service</a> and <a href="#" class="login-link">Privacy Policy</a>
                                        </label>
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-register">
                                            <i class="fas fa-lock me-2"></i> Admin Dashboard
                                        </button>
                                    </div>

                                    <div class="text-center mt-4">
                                        <div class="d-flex flex-wrap justify-content-center">
                                            <span class="feature-badge"><i class="fas fa-check-circle"></i> Full System Control</span>
                                            <span class="feature-badge"><i class="fas fa-check-circle"></i> User Management</span>
                                            <span class="feature-badge"><i class="fas fa-check-circle"></i> Analytics</span>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="text-center pb-4">
                            <p class="mb-0">Already have an account? <a href="login.php" class="login-link">Sign in now</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Add animation to form elements
            document.addEventListener('DOMContentLoaded', function () {
                const formInputs = document.querySelectorAll('.form-control');

                formInputs.forEach(input => {
                    input.addEventListener('focus', function () {
                        this.parentElement.classList.add('input-focused');
                    });

                    input.addEventListener('blur', function () {
                        this.parentElement.classList.remove('input-focused');
                    });
                });

                // Add ripple effect to buttons
                const buttons = document.querySelectorAll('.btn-register');
                buttons.forEach(button => {
                    button.addEventListener('click', function (e) {
                        const x = e.clientX - e.target.getBoundingClientRect().left;
                        const y = e.clientY - e.target.getBoundingClientRect().top;

                        const ripple = document.createElement('span');
                        ripple.classList.add('ripple-effect');
                        ripple.style.left = `${x}px`;
                        ripple.style.top = `${y}px`;

                        this.appendChild(ripple);

                        setTimeout(() => {
                            ripple.remove();
                        }, 1000);
                    });
                });
            });
        </script>
    </body>
</html>