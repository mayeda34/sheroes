<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>sheroes - Find Your Dream Job</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>
            :root {
                --primary-color:rgb(213, 54, 112);
                --secondary-color:rgb(212, 15, 97);
                --accent-color:rgb(220, 24, 155);
                --light-color:rgba(186, 34, 206, 0.32);
                --dark-color:rgb(177, 56, 175);
                --gradient-primary: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            }

            body {
                font-family: 'Poppins', sans-serif;
                background-color:rgb(181, 17, 83);
                color:rgba(212, 25, 118, 0.93);
                overflow-x: hidden;
            }
.navbar-nav .nav-link.active, .navbar-nav .nav-link.show {
    /* color: var(--bs-navbar-active-color); */
    COLOR: HOTPINK;
}.nav-link {
    COLOR: HOTPINK;
    font-weight: 500;
    position: relative;
    margin: 0 10px;
}
            /* Animated Background */
            .bg-animation {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: -2;
                background: linear-gradient(-45deg,rgb(252, 248, 250), #e2e8f0, #f8fafc);
                background-size: 400% 400%;
                animation: gradientBG 15s ease infinite;
            }

            @keyframes gradientBG {
                0% {
                    background-position: 0% 50%;
                }
                50% {
                    background-position: 100% 50%;
                }
                100% {
                    background-position: 0% 50%;
                }
            }

            /* Floating Elements */
            .floating {
                animation: floating 6s ease-in-out infinite;
            }

            @keyframes floating {
                0% {
                    transform: translateY(0px);
                }
                50% {
                    transform: translateY(-15px);
                }
                100% {
                    transform: translateY(0px);
                }
            }

            /* Navbar */
            .navbar {
                background: rgba(231, 14, 155, 0.56);
                backdrop-filter: blur(10px);
                box-shadow: 0 2px 10px rgba(196, 67, 161, 0.1);
            }

            .navbar-brand {
                font-weight: 700;
                font-size: 1.8rem;
                background: var(--gradient-primary);
                -webkit-background-clip: text;
                background-clip: text;
                -webkit-text-fill-color: transparent;
            }

            .nav-link {
                font-weight: 500;
                position: relative;
                margin: 0 10px;
            }

            .nav-link::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                width: 0;
                height: 2px;
                background: var(--primary-color);
                transition: width 0.3s ease;
            }

            .nav-link:hover::after {
                width: 100%;
            }

            /* Hero Section */
            .hero-section {
                min-height: 90vh;
                display: flex;
                align-items: center;
                position: relative;
                overflow: hidden;
            }

            .hero-content {
                position: relative;
                z-index: 1;
            }

            .hero-title {
                font-size: 3.5rem;
                font-weight: 700;
                line-height: 1.2;
                margin-bottom: 1.5rem;
                background: linear-gradient(to right,rgb(204, 35, 120),rgb(205, 42, 191));
                -webkit-background-clip: text;
                background-clip: text;
                -webkit-text-fill-color: transparent;
            }

            .hero-subtitle {
                font-size: 1.25rem;
                color:rgb(212, 31, 128);
                max-width: 600px;
                margin: 0 auto 2.5rem;
            }

            .btn-hero {
                padding: 12px 30px;
                border-radius: 50px;
                font-weight: 600;
                letter-spacing: 0.5px;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
                z-index: 1;
            }

            .btn-hero-primary {
                background: var(--gradient-primary);
                color: white;
                border: none;
                box-shadow: 0 10px 20px rgba(196, 21, 208, 0.3);
            }

            .btn-hero-primary:hover {
                transform: translateY(-3px);
                box-shadow: 0 15px 30px rgba(238, 67, 207, 0.4);
            }

            .btn-hero-outline {
                background: transparent;
                border: 2px solid var(--primary-color);
                color: var(--primary-color);
            }

            .btn-hero-outline:hover {
                background: var(--primary-color);
                color: white;
            }

            /* Hero Illustration */
            .hero-illustration {
                position: relative;
                max-width: 600px;
                margin: 0 auto;
            }

            .hero-img {
                width: 100%;
                height: auto;
                border-radius: 20px;
                box-shadow: 0 25px 50px rgba(163, 54, 118, 0.1);
                transform: perspective(1000px) rotateY(-10deg);
                transition: transform 0.5s ease;
            }

            .hero-img:hover {
                transform: perspective(1000px) rotateY(0deg);
            }

            /* Stats Section */
            .stats-section {
                background: white;
                border-radius: 20px;
                padding: 2rem;
                box-shadow: 0 10px 30px rgba(223, 27, 161, 0.83);
                margin-top: -50px;
                position: relative;
                z-index: 2;
            }

            .stat-item {
                text-align: center;
                padding: 1rem;
            }

            .stat-number {
                font-size: 2.5rem;
                font-weight: 700;
                color: var(--primary-color);
                margin-bottom: 0.5rem;
            }

            .stat-label {
                color:rgb(210, 26, 176);
                font-size: 0.9rem;
                text-transform: uppercase;
                letter-spacing: 1px;
            }

            /* Features Section */
            .features-section {
                padding: 5rem 0;
            }

            .feature-card {
                background: white;
                border-radius: 15px;
                padding: 2rem;
                height: 100%;
                box-shadow: 0 5px 15px rgba(224, 28, 162, 0.88);
                transition: all 0.3s ease;
                border: 1px solid rgba(234, 26, 113, 0.05);
            }

            .feature-card:hover {
                transform: translateY(-10px);
                box-shadow: 0 15px 30px rgba(209, 67, 238, 0.78);
                border-color: rgba(238, 67, 195, 0.82);
            }

            .feature-icon {
                width: 70px;
                height: 70px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: white ;rgba(228, 212, 227, 0.68);
                border-radius: 50%;
                margin-bottom: 1.5rem;
                color: var(--primary-color);
                font-size: 1.5rem;
            }

            /* Footer */
            footer {
                background:rgba(214, 17, 138, 0.94);
                
                padding: 3rem 0 1.5rem;
            }
.fa-brands, .fab {
    COLOR: HOTPINK;
    font-weight: 400;
}
            .footer-links a {
                color:white;
                text-decoration: none;
                transition: all 0.3s ease;
            }
            .list-unstyled {
    COLOR: WHITE;
    padding-left: 0;
    list-style: none;
}
.text-muted {
    --bs-text-opacity: 1;
    color: rgb(230 26 222 / 75%) !important;
}
                   p {
    COLOR: WHITE;
} 

            .footer-links a:hover {
                color: white;
                margin-left: 5px;
            }

            .social-icon {
                width: 40px;
                height: 40px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: WHITE;
               
                border-radius: 50%;
                margin-right: 10px;
                transition: all 0.3s ease;
            }

            .social-icon:hover {
                background COLOR: WHITE;
                transform: translateY(-3px);
            }

            /* Responsive Adjustments */
            @media (max-width: 768px) {
                .hero-title {
                    font-size: 2.5rem;
                }

                .hero-subtitle {
                    font-size: 1.1rem;
                }

                .stats-section {
                    margin-top: 0;
                }
        }
        </style>
    </head>
    <body>
        <!-- Animated Background -->
        <div class="bg-animation"></div>

        <!-- Navbar -->
        <?php include 'home-navbar.php'; ?>

        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 order-lg-1 order-2">
                        <div class="hero-content text-center text-lg-start">
                            <h1 class="hero-title">Empower Your Journey with Sheroes</h1>
                            <p class="hero-subtitle">Join a growing community of strong, independent women. Discover job opportunities tailored to your skills, schedule, and goals.</p>
                            <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-3">
                                <a href="job-listings.php" class="btn btn-hero btn-hero-primary">
                                    <i class="fas fa-search me-2"></i> Browse Jobs
                                </a>
                                <a href="register.php" class="btn btn-hero btn-hero-outline">
                                    <i class="fas fa-user-plus me-2"></i> Get Started
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 order-lg-2 order-1 mb-5 mb-lg-0">
                        <div >
                                <img src="https://www.travisagnew.org/wp-content/uploads/2012/02/Mother.jpg"
                                 alt="Career professionals" class="hero-img img-fluid">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <div class="container">
            <div class="stats-section">
                <div class="row">
                    <div class="col-md-3 col-6">
                        <div class="stat-item">
                            <div class="stat-number">10,000+</div>
                            <div class="stat-label">Jobs Available</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-item">
                            <div class="stat-number">5,000+</div>
                            <div class="stat-label">Companies Hiring</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-item">
                            <div class="stat-number">50,000+</div>
                            <div class="stat-label">Successful Hires</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-item">
                            <div class="stat-number">98%</div>
                            <div class="stat-label">Satisfaction Rate</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <section class="features-section">
            <div class="container">
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-8 text-center">
                        <h2 class="display-5 fw-bold mb-3">Why Choose sheroes?</h2>
                        <p class="text-muted">Our platform offers everything you need to take the next step in your career journey</p>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-lg-4 col-md-6">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-bullseye"></i>
                            </div>
                            <h3>Smart Matching</h3>
                            <p class="text-muted">Our AI-powered algorithm matches you with jobs that fit your skills, experience, and preferences perfectly.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-briefcase"></i>
                            </div>
                            <h3>Top Companies</h3>
                            <p class="text-muted">Access exclusive job opportunities from leading companies across various industries.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h3>Career Growth</h3>
                            <p class="text-muted">Find positions that offer real career progression and professional development opportunities.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <h3>Privacy Focused</h3>
                            <p class="text-muted">Your data is always secure. Control who sees your profile and information.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-comments"></i>
                            </div>
                            <h3>Direct Communication</h3>
                            <p class="text-muted">Chat directly with recruiters and hiring managers through our platform.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <h3>Mobile Friendly</h3>
                            <p class="text-muted">Full access to all features on any device, anytime, anywhere.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="py-5 bg-light">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8 text-center">
                        <h2 class="mb-4">Ready to take the next step in your career?</h2>
                        <a href="register.php" class="btn btn-hero btn-hero-primary btn-lg px-5">
                            <i class="fas fa-rocket me-2"></i> Get Started Now
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer>
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 mb-4">
                        <h4 class="text-white mb-4">SHEROES</h4>
                        <p>Connecting strong moms with real opportunities to build brighter futures.</p>
                        <div class="mt-3">
                            <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-4">
                        <h5 class="text-white mb-4">For Job Seekers</h5>
                        <ul class="list-unstyled footer-links">
                            <li class="mb-2"><a href="#"><i class="fas fa-chevron-right me-2"></i>Browse Jobs</a></li>
                            <li class="mb-2"><a href="#"><i class="fas fa-chevron-right me-2"></i>Create Profile</a></li>
                            <li class="mb-2"><a href="#"><i class="fas fa-chevron-right me-2"></i>Job Alerts</a></li>
                            <li class="mb-2"><a href="#"><i class="fas fa-chevron-right me-2"></i>Career Advice</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-4">
                        <h5 class="text-white mb-4">For Employers</h5>
                        <ul class="list-unstyled footer-links">
                            <li class="mb-2"><a href="#"><i class="fas fa-chevron-right me-2"></i>Post a Job</a></li>
                            <li class="mb-2"><a href="#"><i class="fas fa-chevron-right me-2"></i>Browse Candidates</a></li>
                            <li class="mb-2"><a href="#"><i class="fas fa-chevron-right me-2"></i>Pricing Plans</a></li>
                            <li class="mb-2"><a href="#"><i class="fas fa-chevron-right me-2"></i>HR Solutions</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-4 col-md-4 mb-4">
                        <h5 class="text-white mb-4">Contact Us</h5>
                        <ul class="list-unstyled">
                            <li class="mb-3"><i class="fas fa-map-marker-alt me-2"></i> 123 Food street, Lahore, PK</li>
                            <li class="mb-3"><i class="fas fa-phone me-2"></i> +92 (000) 123-4567</li>
                            <li class="mb-3"><i class="fas fa-envelope me-2"></i> info@sheroes.com</li>
                        </ul>
                    </div>
                </div>
                <hr class="my-4 bg-secondary">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start">
                        <p class="mb-0">&copy; <?= date("Y"); ?> Sheroes. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <a href="#" class="text-white me-3">Privacy Policy</a>
                        <a href="#" class="text-white me-3">Terms of Service</a>
                        <a href="#" class="text-white">Cookie Policy</a>
                    </div>
                </div>
            </div>
        </footer>
    </body>
</html>