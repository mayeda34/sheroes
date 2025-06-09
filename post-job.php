<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
if ($_SESSION['role'] != 'employer') {
    header("Location: unauthorized.php");
    exit();
}
include 'config.php';

$employerId = $_SESSION['user_id'];
$errors = [];
$success = '';

// Get employer details for default company name
$query = "SELECT CompanyName FROM Employer WHERE Id = $employerId";
$result = mysqli_query($conn, $query);
$employer = mysqli_fetch_assoc($result);

// Get job categories
$categories = [];
$query = "SELECT * FROM JobCategory";
$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jobTitle = trim($_POST['job_title']);
    $companyName = trim($_POST['company_name']);
    $location = trim($_POST['location']);
    $requirements = trim($_POST['requirements']);
    $salaryRange = trim($_POST['salary_range']);
    $jobType = $_POST['job_type'];
    $deadline = $_POST['deadline'];
    $categoryId = $_POST['category_id'];

    // Validate inputs
    if (empty($jobTitle))
        $errors[] = "Job title is required";
    if (empty($companyName))
        $errors[] = "Company name is required";
    if (empty($location))
        $errors[] = "Location is required";
    if (empty($requirements))
        $errors[] = "Job requirements are required";
    if (empty($deadline))
        $errors[] = "Application deadline is required";
    if (strtotime($deadline) < strtotime('today'))
        $errors[] = "Deadline must be in the future";

    if (empty($errors)) {
        $query = "INSERT INTO Job (
            EmployerId, JobTitle, CompanyName, Location, JobRequirements, 
            SalaryRange, JobType, ApplicationDeadline, CategoryId
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?
        )";

        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'isssssssi',
                $employerId, $jobTitle, $companyName, $location, $requirements,
                $salaryRange, $jobType, $deadline, $categoryId
        );

        if (mysqli_stmt_execute($stmt)) {
            $success = "Job posted successfully! It will be visible after admin approval.";
            $_POST = []; // Clear form
        } else {
            $errors[] = "Error posting job: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Post New Job | sheroes</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <style>
    
            body {
                font-family: 'Inter', sans-serif;
                background-color: #f0f4f8;
            }
            .navbar {
                background-color:HOTPINK;
                padding: 1rem 2rem;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }
            .navbar-brand {
                color: white;
                font-size: 1.8rem;
                font-weight: 600;
            }
            .navbar-nav .nav-link {
                color: rgba(255, 255, 255, 0.8);
                font-size: 1.1rem;
                margin-left: 1rem;
                transition: color 0.3s ease, background-color 0.3s ease;
                border-radius: 8px;
                padding: 0.5rem 1rem;
            }
            .navbar-nav .nav-link:hover,
            .navbar-nav .nav-link.active {
                color: white;
                background-color: rgba(255, 255, 255, 0.1);
            }
            .navbar-nav .nav-link i {
                margin-right: 8px;
            }
            .logout-btn {
                color: #ffffff;
                background-color:HOTPINK;
                border: none;
                padding: 0.75rem 1.5rem;
                border-radius: 8px;
                font-size: 1.1rem;
                transition: background-color 0.3s ease, transform 0.2s ease;
                margin-left: 1rem;
                font-weight: 500;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            }.active>.page-link, .page-link.active {
    z-index: 3;
    color: HOTPINK;
    background-color:HOTPINK;
    border-color: HOTPINK;
}
            .logout-btn:hover {
                background-color:HOTPINK;
                transform: translateY(-2px);
                box-shadow: 0 3px 7px rgba(0, 0, 0, 0.15);
            }
            .main-content {
                padding: 2rem;
            }
            .welcome-header {
                background-color:HOTPINK;
                color:WHITE;
                padding: 2.5rem;
                border-radius: 12px;
                margin-bottom: 2.5rem;
                box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
                text-align: center;
            }
            .welcome-header h1 {
                font-size: 2.2rem;
                font-weight: 700;
                margin-bottom: 1rem;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
            }
            .welcome-header p {
                font-size: 1.2rem;
                color: #555;
            }
            .dashboard-card {
                background-color: #fff;
                border-radius: 12px;
                margin-bottom: 2.5rem;
                box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                border: none;
            }
            .dashboard-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
            }
            .dashboard-card-body {
                padding: 2rem;
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
            }.btn-primary {
    --bs-btn-color: #fff;
    --bs-btn-bg: HOTPINK;
    --bs-btn-border-color: HOTPINK;
    --bs-btn-hover-color: #fff;
    --bs-btn-hover-bg: HOTPINK;
    --bs-btn-hover-border-color: HOTPINK;
    --bs-btn-focus-shadow-rgb: 49,132,253;
    --bs-btn-active-color: #fff;
    --bs-btn-active-bg: HOTPINK;
    --bs-btn-active-border-color: HOTPINK;
    --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
    --bs-btn-disabled-color: #fff;
    --bs-btn-disabled-bg: HOTPIN;
    --bs-btn-disabled-border-color: HOTPINK;
}
h1 {
    color: hotpink;
}
form {
    color: hotpink;
}
            .dashboard-card-title {
                font-size: 1.5rem;
                color:HOTPINK;
                margin-bottom: 1.5rem;
                display: flex;
                align-items: center;
                gap: 10px;
                font-weight: 600;
            }
            .dashboard-card-value {
                font-size: 2.5rem;
                font-weight: 700;
                color:HOTPINK1;
                margin-bottom: 2rem;
            }
            .view-all-btn {
                background-color:HOTPINK;
                color: #fff;
                padding: 0.75rem 1.5rem;
                border-radius: 8px;
                font-size: 1.1rem;
                transition: background-color 0.3s ease, transform 0.2s ease;
                border: none;
                font-weight: 500;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            }
            .view-all-btn:hover {
                background-color: HOTPINK;
                transform: translateY(-2px);
                box-shadow: 0 3px 7px rgba(0, 0, 0, 0.15);
            }
            .recent-jobs-card .card-header,
            .recent-applications-card .card-header {
                background-color: #f7fafc;
                padding: 1.5rem;
                border-bottom: 2px solid #e0e0e0;
                border-top-left-radius: 12px;
                border-top-right-radius: 12px;
            }
            .recent-jobs-card .card-header h5,
            .recent-applications-card .card-header h5 {
                font-size: 1.2rem;
                font-weight: 600;
                color:HOTPINK;
                margin-bottom: 0;
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .recent-jobs-card .card-body,
            .recent-applications-card .card-body {
                padding: 1.5rem;
            }
            .list-group-item {
                border-radius: 8px;
                margin-bottom: 0.75rem;
                border: 1px solid #e0e0e0;
                padding: 1rem;
                background-color: #fff;
                transition: background-color 0.3s ease, transform 0.2s ease;
            }
            .list-group-item:hover {
                background-color: #f7fafc;
                transform: translateY(-2px);
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            }
            .list-group-item strong {
                color: HOTPINK;
                font-size: 1.1rem;
            }
            .list-group-item small {
                color: #7f8c8d;
                display: block;
                font-size: 0.9rem;
            }
            .list-group-item .badge {
                border-radius: 12px;
                padding: 0.5rem 1rem;
                font-size: 0.85rem;
                font-weight: 500;
            }
            .badge-success {
                background-color:HOTPINK;
                color: #fff;
            }
            .badge-warning {
                background-color: HOTPINK;
                color:HOTPINK;
            }
            .badge-danger {
                background-color: HOTPINK;
                color: #fff;
            }
            .badge-primary {
                background-color: HOTPINK;
                color: #fff;
            }
            .badge-info {
                background-color: HOTPINK;
                color: #fff;
            }
            .mt-4 {
                margin-top: 2.5rem;
            }
        </style>
    </head>
    <body>
   <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand" href="employer-dashboard.php">SHEROES</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link active" href="employer-dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="post-job.php">
                                <i class="fas fa-plus-circle"></i> Post New Job
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage-posted-jobs.php">
                                <i class="fas fa-briefcase"></i> My Job Postings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="applications.php">
                                <i class="fas fa-file-alt"></i> Applications
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="company-profile.php">
                                <i class="fas fa-building"></i> Company Profile
                            </a>
                        </li>
                    </ul>
                    <a href="logout.php" class="btn logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </nav>

        <main class="container">
            <div class="card">
                <div class="card-header">
                    <h1><i class="fas fa-plus-circle"></i> Post New Job</h1>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> Please fix the following issues:
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= $success ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="post-job.php" method="POST">
                        <div class="form-group">
                            <label for="job_title" class="form-label">Job Title *</label>
                            <input type="text" class="form-control" id="job_title" name="job_title" value="<?= $_POST['job_title'] ?? '' ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="company_name" class="form-label">Company Name *</label>
                            <input type="text" class="form-control" id="company_name" name="company_name" value="<?= $_POST['company_name'] ?? $employer['CompanyName'] ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="location" class="form-label">Location *</label>
                            <input type="text" class="form-control" id="location" name="location" value="<?= $_POST['location'] ?? '' ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="salary_range" class="form-label">Salary Range</label>
                            <input type="text" class="form-control" id="salary_range" name="salary_range" value="<?= $_POST['salary_range'] ?? '' ?>" placeholder="e.g. $50,000 - $70,000 per year">
                        </div>
                        <div class="form-group">
                            <label for="job_type" class="form-label">Job Type *</label>
                            <select class="form-select" id="job_type" name="job_type" required>
                                <option value="">Select Job Type</option>
                                <option value="Full-time" <?= ($_POST['job_type'] ?? '') == 'Full-time' ? 'selected' : '' ?>>Full-time</option>
                                <option value="Part-time" <?= ($_POST['job_type'] ?? '') == 'Part-time' ? 'selected' : '' ?>>Part-time</option>
                                <option value="Contract" <?= ($_POST['job_type'] ?? '') == 'Contract' ? 'selected' : '' ?>>Contract</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="category_id" class="form-label">Job Category *</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['Id'] ?>" <?= ($_POST['category_id'] ?? '') == $category['Id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['CategoryName']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="deadline" class="form-label">Application Deadline *</label>
                            <input type="date" class="form-control" id="deadline" name="deadline" value="<?= $_POST['deadline'] ?? '' ?>" required min="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="form-group">
                            <label for="requirements" class="form-label">Job Requirements *</label>
                            <textarea class="form-control" id="requirements" name="requirements" rows="6" required><?= $_POST['requirements'] ?? '' ?></textarea>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane"></i> Post Job
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Set minimum date for deadline (today)
            document.getElementById('deadline').min = new Date().toISOString().split('T')[0];
        </script>
    </body>
</html>
