<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'jobseeker') {
    header("Location: login.php");
    exit();
}
include 'config.php';

$jobId = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;
$jobSeekerId = $_SESSION['user_id'];

if ($jobId <= 0) {
    header("Location: browse-jobs.php");
    exit();
}

// Check if job exists and is approved
$jobQuery = "SELECT j.Id, j.JobTitle, j.CompanyName 
             FROM Job j 
             WHERE j.Id = $jobId AND j.Status = 'Approved'";
$jobResult = mysqli_query($conn, $jobQuery);

if (mysqli_num_rows($jobResult) == 0) {
    header("Location: browse-jobs.php");
    exit();
}

$job = mysqli_fetch_assoc($jobResult);

// Check if already applied
$checkQuery = "SELECT Id FROM Application 
               WHERE JobSeekerId = $jobSeekerId AND JobId = $jobId";
$checkResult = mysqli_query($conn, $checkQuery);

if (mysqli_num_rows($checkResult) > 0) {
    // Already applied, show message but don't insert again
    $alreadyApplied = true;
} else {
    // Insert new application
    $insertQuery = "INSERT INTO Application 
                   (JobId, JobSeekerId, ApplicationDate, Status) 
                   VALUES ($jobId, $jobSeekerId, NOW(), 'Applied')";
    
    if (mysqli_query($conn, $insertQuery)) {
        $applicationSuccess = true;
    } else {
        $applicationError = true;
            }}
        ?>
       
<!DOCTYPE html>
<html>
    <head>
        <title>Application | sheroes</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <style>
            .application-card {
                max-width: 600px;
                margin: 0 auto;
            }
            .navbar-brand {
    padding-top: var(--bs-navbar-brand-padding-y);
    padding-bottom: var(--bs-navbar-brand-padding-y);
    margin-right: var(--bs-navbar-brand-margin-end);
    font-size: var(--bs-navbar-brand-font-size);
    color: hotpink;
    text-decoration: none;
    white-space: nowrap;
}
.btn-primary {
    --bs-btn-color: #fff;
    --bs-btn-bg:hotpink;
    --bs-btn-border-color:hotpink;
    --bs-btn-hover-color: #fff;
    --bs-btn-hover-bg: hotpink;
    --bs-btn-hover-border-color:hotpink;
    --bs-btn-focus-shadow-rgb: 49,132,253;
    --bs-btn-active-color: #fff;
    --bs-btn-active-bg:hotpink;
    --bs-btn-active-border-color:hotpink;
    --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
    --bs-btn-disabled-color: #fff;
    --bs-btn-disabled-bg:hotpink;
    --bs-btn-disabled-border-color:hotpink;
}.btn-outline-primary {
    --bs-btn-color:hotpink;
    --bs-btn-border-color:hotpink;
    --bs-btn-hover-color: #fff;
    --bs-btn-hover-bg: hotpink;
    --bs-btn-hover-border-color: hotpink;
    --bs-btn-focus-shadow-rgb: 13,110,253;
    --bs-btn-active-color: #fff;
    --bs-btn-active-bg: hotpink;
    --bs-btn-active-border-color:hotpink;
    --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
    --bs-btn-disabled-color:hotpink;
    --bs-btn-disabled-bg: transparent;
    --bs-btn-disabled-border-color:hotpink;
    --bs-gradient: none;
}.btn-outline-secondary {
    --bs-btn-color:hotpink;
    --bs-btn-border-color: hotpink;
    --bs-btn-hover-color: #fff;
    --bs-btn-hover-bg: hotpink;
    --bs-btn-hover-border-color: hotpink;
    --bs-btn-focus-shadow-rgb: 108,117,125;
    --bs-btn-active-color: #fff;
    --bs-btn-active-bg:hotpink;
    --bs-btn-active-border-color: hotpink;
    --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
    --bs-btn-disabled-color: hotpink;
    --bs-btn-disabled-bg: transparent;
    --bs-btn-disabled-border-color:hotpink;
    --bs-gradient: none;
}.navbar-nav .nav-link.active, .navbar-nav .nav-link.show {
    color: hotpink;
}.nav-link {
    display: block;
    padding: var(--bs-nav-link-padding-y) var(--bs-nav-link-padding-x);
    font-size: var(--bs-nav-link-font-size);
    font-weight: var(--bs-nav-link-font-weight);
    color: hotpink;
    text-decoration: none;
    background: 0 0;
    border: 0;
    transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out;
}
        </style>
    </head>
    <body>
        <?php include 'includes/jobseeker-navbar.php'; ?>

        <div class="container py-5">
            <div class="card application-card">
                <div class="card-body text-center">
                    <?php if (isset($alreadyApplied)): ?>
                        <i class="fas fa-info-circle text-primary fa-5x mb-4"></i>
                        <h2>Already Applied</h2>
                        <p class="lead">You've already applied for <strong><?= htmlspecialchars($job['JobTitle']) ?></strong> at <strong><?= htmlspecialchars($job['CompanyName']) ?></strong>.</p>
                    <?php elseif (isset($applicationError)): ?>
                        <i class="fas fa-exclamation-circle text-danger fa-5x mb-4"></i>
                        <h2>Application Error</h2>
                        <p class="lead">There was an error submitting your application. Please try again.</p>
                    <?php else: ?>
                        <i class="fas fa-check-circle text-success fa-5x mb-4"></i>
                        <h2>Application Submitted!</h2>
                        <p class="lead">Your application for <strong><?= htmlspecialchars($job['JobTitle']) ?></strong> at <strong><?= htmlspecialchars($job['CompanyName']) ?></strong> has been successfully submitted.</p>
                    <?php endif; ?>

                    <div class="d-grid gap-2 col-md-6 mx-auto mt-4">
                        <a href="job-details.php?id=<?= $jobId ?>" class="btn btn-primary">
                            <i class="fas fa-briefcase"></i> View Job Details
                        </a>
                        <a href="my-applications.php" class="btn btn-outline-primary">
                            <i class="fas fa-list"></i> My Applications
                        </a>
                        <a href="browse-jobs.php" class="btn btn-outline-secondary">
                            <i class="fas fa-search"></i> Browse More Jobs
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>