<?php
include 'config.php';

// Get search and filter parameters
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$category = isset($_GET['category']) ? intval($_GET['category']) : 0;
$location = isset($_GET['location']) ? mysqli_real_escape_string($conn, $_GET['location']) : '';
$jobType = isset($_GET['job_type']) ? $_GET['job_type'] : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 10;

// Base query - only show approved jobs
$query = "SELECT j.*, e.CompanyName, c.CategoryName, e.Logo 
          FROM Job j
          JOIN Employer e ON j.EmployerId = e.Id
          LEFT JOIN JobCategory c ON j.CategoryId = c.Id
          WHERE j.Status = 'Approved'";

// Add filters
if (!empty($search)) {
    $query .= " AND (j.JobTitle LIKE '%$search%' OR j.JobRequirements LIKE '%$search%' OR e.CompanyName LIKE '%$search%')";
}
if ($category > 0) {
    $query .= " AND j.CategoryId = $category";
}
if (!empty($location)) {
    $query .= " AND j.Location LIKE '%$location%'";
}
if (!empty($jobType)) {
    $query .= " AND j.JobType = '$jobType'";
}

// Get total count for pagination
$countQuery = str_replace('j.*, e.CompanyName, c.CategoryName, e.Logo', 'COUNT(*) as total', $query);
$countResult = mysqli_query($conn, $countQuery);
$totalJobs = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalJobs / $perPage);

// Add sorting and pagination
$query .= " ORDER BY j.CreatedAt DESC LIMIT " . (($page - 1) * $perPage) . ", $perPage";
$result = mysqli_query($conn, $query);

// Get categories for filter dropdown
$categories = mysqli_query($conn, "SELECT * FROM JobCategory");
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Job Listings | SHEROES</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>
            :root {
                --primary-color:HOTPINK;
                --secondary-color: hotpink;
                --accent-color:HOTPINK;
                --light-color: #f8f9fa;
                --dark-color: hotpink;
                --gradient-primary: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            }
            
            body {
                font-family: 'Poppins', sans-serif;
                background-color: #f8fafc;
                color:HOTPINK;
            }
            
            .navbar {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                box-shadow: 0 2px 10px hotpink;
            }
            
            .navbar-brand {
                font-weight: 700;
                font-size: 1.5rem;
                background: var(--gradient-primary);
                -webkit-background-clip: text;
                background-clip: text;
                -webkit-text-fill-color: transparent;
            }
            
            .hero-section {
                background: url('https://tse3.mm.bing.net/th?id=OIP.3tE7GuBBXKlzfM53l7ufwQHaDF&pid=Api&P=0&h=220') no-repeat center center;
                background-size: cover;
                position: relative;
                padding: 5rem 0;
                margin-bottom: 3rem;
                border-radius: 0 0 20px 20px;
                overflow: hidden;
                
            }
            
            .hero-section::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: hotpink;
            }
            
            .hero-content {
                position: relative;
                z-index: 1;
                color: white;
            }
            
            .filter-section {
                background: white;
                border-radius: 15px;
                padding: 2rem;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
                margin-top: -50px;
                position: relative;
                z-index: 2;
            }
            
            .job-card {
                border: none;
                border-radius: 15px;
                overflow: hidden;
                box-shadow: hotpink;
                transition: all 0.3s ease;
                margin-bottom: 1.5rem;
                background: white;
            }
            
            .job-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 15px 30px hotpink;
            }
            
            .company-logo {
                width: 60px;
                height: 60px;
                object-fit: contain;
                border-radius: 10px;
                border: 1px solid #e2e8f0;
                background: white;
                padding: 5px;
            }
            
            .job-type-badge {
                padding: 5px 12px;
                border-radius: 20px;
                font-weight: 500;
                font-size: 0.8rem;
            }a.nav-link {
    color: hotpink;
}.navbar-nav .nav-link.active, .navbar-nav .nav-link.show {
    color: hotpink;
}
            
            .job-type-fulltime {
                background: hotpink;
                color:white;
            }
            .text-primary {
    --bs-text-opacity: 1;
    color: hotpink!important;
}
            .job-type-parttime {
                background: hotpink;
                color:white;
            }
            
            .job-type-contract {
                background: hotpink;
                color:hotpink;
            }
            
            .job-meta {
                color:hotpink;
                font-size: 0.9rem;
            }
            
            .job-meta i {
                width: 20px;
                text-align: center;
                margin-right: 5px;
            }
            
            .job-description {
                display: -webkit-box;
                -webkit-line-clamp: 3;
                -webkit-box-orient: vertical;
                overflow: hidden;
                color:hotpink;
            }
            .btn-outline-primary {
    --bs-btn-color:hotpink;
    --bs-btn-border-color:hotpink;
    --bs-btn-hover-color: #fff;
    --bs-btn-hover-bg: hotpink;
    --bs-btn-hover-border-color:hotpink;
    --bs-btn-focus-shadow-rgb: 13,110,253;
    --bs-btn-active-color: #fff;
    --bs-btn-active-bg: hotpink;
    --bs-btn-active-border-color: hotpink;
    --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
    --bs-btn-disabled-color:hotpink;
    --bs-btn-disabled-bg: transparent;
    --bs-btn-disabled-border-color:hotpink;
    --bs-gradient: none;
}.btn-primary {
    --bs-btn-color: #fff;
    --bs-btn-bg:hotpink;
    --bs-btn-border-color: hotpink;
    --bs-btn-hover-color: #fff;
    --bs-btn-hover-bg: hotpink;
    --bs-btn-hover-border-color:hotpink;
    --bs-btn-focus-shadow-rgb: 49,132,253;
    --bs-btn-active-color: #fff;
    --bs-btn-active-bg:hotpink;
    --bs-btn-active-border-color: hotpink;
    --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
    --bs-btn-disabled-color: #fff;
    --bs-btn-disabled-bg:hotpink;
    --bs-btn-disabled-border-color:hotpink;
}
            
            .btn-apply {
                background: var(--gradient-primary);
                border: none;
                font-weight: 500;
                padding: 8px 20px;
                border-radius: 8px;
                transition: all 0.3s ease;
            }
            
            .btn-apply:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px hotpink;
            }
            a.btn.btn-sm.btn-outline-primary.me-2 {
    color: hotpink;
}
            
            .login-prompt {
    background: white;
    border-left: 4px solid var(--primary-color);
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}
            
            .pagination .page-item.active .page-link {
                background: var(--gradient-primary);
                border-color: transparent;
            }
            
            .pagination .page-link {
                color: var(--primary-color);
                border-radius: 8px;
                margin: 0 5px;
                border: none;
                box-shadow: none;
            }
            
            .no-jobs {
                background: white;
                border-radius: 15px;
                padding: 3rem;
                text-align: center;
            }
            
            .no-jobs-icon {
                font-size: 3rem;
                color: hotpink;
                margin-bottom: 1rem;
            }
            
            footer {
                background:hotpink;
                color: white;
                padding: 3rem 0 1.5rem;
                margin-top: 5rem;
            }
            
            
            .footer-links a:hover {
                color: white;
                margin-left: 5px;
            }
            .footer-links a {
    color: white;
    text-decoration: none;
    transition: all 0.3s ease;
}
            
            .social-icon {
                width: 40px;
                height: 40px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: hotpink;
                border-radius: 50%;
                margin-right: 10px;
                transition: all 0.3s ease;
            }
            
            .social-icon:hover {
                background: var(--primary-color);
                transform: translateY(-3px);
            }
            
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            .animated-card {
                animation: fadeIn 0.5s ease-out;
            }
            
            /* Responsive adjustments */
            @media (max-width: 768px) {
                .filter-section {
                    margin-top: 0;
                }
                
                .hero-section {
                    padding: 3rem 0;
                    border-radius: 0;
                }
            }
        </style>
    </head>
    <body>
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light sticky-top">
            <div class="container">
                <a class="navbar-brand" href="index.php">SHEROES</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown"
                        aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
                    <ul class="navbar-nav fw-semibold">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php"><i class="fas fa-home me-1"></i>Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt me-1"></i>Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php"><i class="fas fa-user-plus me-1"></i>Register</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="job-listings.php"><i class="fas fa-briefcase me-1"></i>Jobs</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container">
                <div class="hero-content text-center">
                    <h1 class="display-5 fw-bold mb-3">Empower Your Journey with Sheroes</h1>
                    <p class="lead mb-4">Browse thousands of job listings from top companies worldwide</p>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <div class="container mb-5">
            <!-- Filter Section -->
            <div class="filter-section">
                <form method="GET" action="job-listings.php">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                                <input type="text" name="search" class="form-control" placeholder="Job title, company, or keywords" value="<?= htmlspecialchars($search) ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select name="category" class="form-select">
                                <option value="">All Categories</option>
                                <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                                    <option value="<?= $cat['Id'] ?>" <?= $category == $cat['Id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['CategoryName']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="job_type" class="form-select">
                                <option value="">All Types</option>
                                <option value="Full-time" <?= $jobType == 'Full-time' ? 'selected' : '' ?>>Full-time</option>
                                <option value="Part-time" <?= $jobType == 'Part-time' ? 'selected' : '' ?>>Part-time</option>
                                <option value="Contract" <?= $jobType == 'Contract' ? 'selected' : '' ?>>Contract</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-map-marker-alt"></i></span>
                                <input type="text" name="location" class="form-control" placeholder="Location" value="<?= htmlspecialchars($location) ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i> Filter</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Login Prompt -->
            <div class="login-prompt">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <div class="mb-3 mb-md-0">
                        <h5 class="mb-1">Unlock more opportunities</h5>
                        <p class="mb-0">Create an account to save jobs, get alerts, and track applications</p>
                    </div>
                    <div class="d-flex">
                        <a href="login.php" class="btn btn-outline-primary me-2"><i class="fas fa-sign-in-alt me-1"></i> Login</a>
                        <a href="register.php" class="btn btn-primary"><i class="fas fa-user-plus me-1"></i> Register</a>
                    </div>
                </div>
            </div>

            <!-- Results Header -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
                <h4 class="mb-3 mb-md-0"><?= number_format($totalJobs) ?> Jobs Available</h4>
                <div class="d-flex">
                    <div class="dropdown me-2">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-sort me-1"></i> Sort By
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="?<?= http_build_query(array_merge($_GET, ['sort' => 'newest'])) ?>">Newest First</a></li>
                            <li><a class="dropdown-item" href="?<?= http_build_query(array_merge($_GET, ['sort' => 'oldest'])) ?>">Oldest First</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Job Listings -->
            <div class="row">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($job = mysqli_fetch_assoc($result)): ?>
                        <div class="col-lg-6 mb-4 animated-card">
                            <div class="job-card h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-start mb-3">
                                        <?php if (!empty($job['Logo'])): ?>
                                            <img src="<?= htmlspecialchars($job['Logo']) ?>" alt="<?= htmlspecialchars($job['CompanyName']) ?>" class="company-logo me-3">
                                        <?php else: ?>
                                            <div class="company-logo me-3 d-flex align-items-center justify-content-center bg-light">
                                                <i class="fas fa-building text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="flex-grow-1">
                                            <h5 class="card-title mb-1"><?= htmlspecialchars($job['JobTitle']) ?></h5>
                                            <h6 class="card-subtitle text-primary"><?= htmlspecialchars($job['CompanyName']) ?></h6>
                                        </div>
                                        <span class="job-type-badge job-type-<?= strtolower(str_replace('-', '', $job['JobType'])) ?>">
                                            <?= $job['JobType'] ?>
                                        </span>
                                    </div>

                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        <?php if ($job['CategoryName']): ?>
                                            <span class="badge bg-light text-dark"><i class="fas fa-tag me-1"></i> <?= $job['CategoryName'] ?></span>
                                        <?php endif; ?>
                                        <span class="badge bg-light text-dark"><i class="fas fa-map-marker-alt me-1"></i> <?= htmlspecialchars($job['Location']) ?></span>
                                        <?php if ($job['SalaryRange']): ?>
                                            <span class="badge bg-light text-dark"><i class="fas fa-money-bill-wave me-1"></i> <?= htmlspecialchars($job['SalaryRange']) ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="job-description mb-4">
                                        <?= nl2br(htmlspecialchars(substr($job['JobRequirements'], 0, 200))) ?>...
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted"><i class="far fa-clock me-1"></i> <?= date('M d, Y', strtotime($job['CreatedAt'])) ?></small>
                                        <div>
                                            <a href="job-details.php?id=<?= $job['Id'] ?>" class="btn btn-sm btn-outline-primary me-2">Details</a>
                                            <a href="login.php?redirect=apply-job.php?job_id=<?= $job['Id'] ?>" class="btn btn-sm btn-apply">Apply Now</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="no-jobs">
                            <div class="no-jobs-icon">
                                <i class="fas fa-search"></i>
                            </div>
                            <h4 class="mb-3">No jobs found matching your criteria</h4>
                            <p class="text-muted mb-4">Try adjusting your search filters or browse all available positions</p>
                            <a href="job-listings.php" class="btn btn-primary">Clear Filters</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Job pagination" class="mt-5">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>

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
                            <li class="mb-3"><i class="fas fa-map-marker-alt me-2"></i>123 Food street, Lahore, PK</li>
                            <li class="mb-3"><i class="fas fa-phone me-2"></i>+92 (000) 123-4567</li>
                            <li class="mb-3"><i class="fas fa-envelope me-2"></i>info@sheroes.com</li>
                        </ul>
                    </div>
                </div>
                <hr class="my-4 bg-secondary">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start">
                        <p class="mb-0">&copy; <?= date("Y") ?> SHEROES. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <a href="#" class="text-white me-3">Privacy Policy</a>
                        <a href="#" class="text-white me-3">Terms of Service</a>
                        <a href="#" class="text-white">Cookie Policy</a>
                    </div>
                </div>
            </div>
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Add animation to form elements
            document.addEventListener('DOMContentLoaded', function() {
                // Add focus styles to form inputs
                const formInputs = document.querySelectorAll('.form-control, .form-select');
                
                formInputs.forEach(input => {
                    input.addEventListener('focus', function() {
                        this.parentElement.classList.add('input-focused');
                    });
                    
                    input.addEventListener('blur', function() {
                        this.parentElement.classList.remove('input-focused');
                    });
                });
                
                // Animate job cards sequentially
                const jobCards = document.querySelectorAll('.animated-card');
                jobCards.forEach((card, index) => {
                    card.style.animationDelay = `${index * 0.1}s`;
                });
            });
        </script>
    </body>
</html>