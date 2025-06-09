<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';

// Get job ID from URL
$jobId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($jobId <= 0) {
    header("Location: browse-jobs.php");
    exit();
}

// Fetch job details
$query = "SELECT j.*, e.CompanyName, e.ContactPerson, e.Email as EmployerEmail, e.Phone as EmployerPhone, 
                 e.Location as CompanyLocation, c.CategoryName
          FROM Job j
          JOIN Employer e ON j.EmployerId = e.Id
          LEFT JOIN JobCategory c ON j.CategoryId = c.Id
          WHERE j.Id = $jobId AND j.Status = 'Approved'";
$result = mysqli_query($conn, $query);
$job = mysqli_fetch_assoc($result);

if (!$job) {
    header("Location: browse-jobs.php");
    exit();
}

// Check if job is saved (for job seekers)
$isSaved = false;
if ($_SESSION['role'] == 'jobseeker') {
    $jobSeekerId = $_SESSION['user_id'];
    $savedQuery = "SELECT Id FROM SavedJobs WHERE JobSeekerId = $jobSeekerId AND JobId = $jobId";
    $isSaved = mysqli_num_rows(mysqli_query($conn, $savedQuery)) > 0;
}

// Check if already applied (for job seekers)
$hasApplied = false;
if ($_SESSION['role'] == 'jobseeker') {
    $appliedQuery = "SELECT Id FROM Application WHERE JobSeekerId = $jobSeekerId AND JobId = $jobId";
    $hasApplied = mysqli_num_rows(mysqli_query($conn, $appliedQuery)) > 0;
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= htmlspecialchars($job['JobTitle']) ?> | sheroes</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <style>
            .job-header {
                background-color: #f8f9fa;
                border-radius: 10px;
                padding: 30px;
                margin-bottom: 30px;
            }
            .job-details-section {
                background-color: white;
                border-radius: 10px;
                padding: 25px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            }.bg-primary {
    --bs-bg-opacity: 1;
    background-color: hotpink!important;
}.btn-outline-secondary {
    --bs-btn-color:hotpink;
    --bs-btn-border-color:hotpink;
    --bs-btn-hover-color: #fff;
    --bs-btn-hover-bg: hotpink;
    --bs-btn-hover-border-color:hotpink;
    --bs-btn-focus-shadow-rgb: 108,117,125;
    --bs-btn-active-color: #fff;
    --bs-btn-active-bg:hotpink;
    --bs-btn-active-border-color:hotpink;
    --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
    --bs-btn-disabled-color:hotpink;
    --bs-btn-disabled-bg: transparent;
    --bs-btn-disabled-border-color: hotpink;
    --bs-gradient: none;
}
.btn.disabled, .btn:disabled, fieldset:disabled .btn {
    color: var(--bs-btn-disabled-color);
    pointer-events: none;
    background-color: hotpink;
    border-color: hotpink;
    opacity: var(--bs-btn-disabled-opacity);
}.btn-outline-primary {
    --bs-btn-hover-color: #fff;
    --bs-btn-hover-bg: hotpink;
    --bs-btn-hover-border-color: hotpink;
    --bs-btn-focus-shadow-rgb: 13,110,253;
    --bs-btn-active-color: #fff;
    --bs-btn-active-bg: hotpink;
    --bs-btn-active-border-color: hotpink;
    --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
    --bs-btn-disabled-color: hotpink;
    --bs-btn-disabled-bg: transparent;
    --bs-btn-disabled-border-color: hotpink;
    --bs-gradient: none;
}.btn-outline-primary {
    --bs-btn-color: black;
    --bs-btn-border-color: hotpink;
    --bs-btn-hover-color: #fff;}
 a.navbar-brand {
    color: hotpink !important;
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
}h3.mb-4 {
    color: hotpink;
}.card-title {
    margin-bottom: var(--bs-card-title-spacer-y);
    color: hotpink;
}

            .btn-primary {
    --bs-btn-color: #fff;
    --bs-btn-bg: hotpink;
    --bs-btn-border-color:hotpink;
    --bs-btn-hover-color: #fff;
    --bs-btn-hover-bg: hotpink;
    --bs-btn-hover-border-color:hotpink;
    --bs-btn-focus-shadow-rgb: 49,132,253;
    --bs-btn-active-color: #fff;
    --bs-btn-active-bg: hotpink;
    --bs-btn-active-border-color:hotpink;
    --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
    --bs-btn-disabled-color: #fff;
    --bs-btn-disabled-bg:hotpink;
    --bs-btn-disabled-border-color:hotpink;
}
            .company-logo {
                width: 100px;
                height: 100px;
                object-fit: contain;
                border-radius: 10px;
                border: 1px solid #eee;
                padding: 5px;
                background-color: white;
            }
            .badge-job-type {
                font-size: 0.9rem;
                padding: 5px 10px;
            }
            .job-actions {
                position: sticky;
                top: 20px;
            }
        </style>
    </head>
    <body>
    <?php include 'includes/jobseeker-navbar.php'; ?>

        <div class="container py-5">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Job Header -->
                    <div class="job-header">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h1><?= htmlspecialchars($job['JobTitle']) ?></h1>
                                <h2 class="h4 text-muted"><?= htmlspecialchars($job['CompanyName']) ?></h2>
                                <div class="d-flex gap-2 my-2">
                                    <span class="badge bg-primary badge-job-type"><?= $job['JobType'] ?></span>
                                    <?php if ($job['CategoryName']): ?>
                                        <span class="badge bg-secondary badge-job-type"><?= $job['CategoryName'] ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($job['CompanyName']) ?>&size=100&background=random" 
                                 alt="Company Logo" class="company-logo">
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-4">
                                <p><i class="fas fa-map-marker-alt text-primary"></i> <strong>Location:</strong> <?= htmlspecialchars($job['Location']) ?></p>
                            </div>
                            <div class="col-md-4">
                                <p><i class="fas fa-money-bill-wave text-primary"></i> <strong>Salary:</strong> <?= htmlspecialchars($job['SalaryRange'] ?: 'Negotiable') ?></p>
                            </div>
                            <div class="col-md-4">
                                <p><i class="fas fa-calendar-alt text-primary"></i> <strong>Deadline:</strong> <?= date('M d, Y', strtotime($job['ApplicationDeadline'])) ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Job Details -->
                    <div class="job-details-section mb-4">
                        <h3 class="mb-4"><i class="fas fa-info-circle"></i> Job Description</h3>
                        <div class="mb-4">
                            <?= nl2br(htmlspecialchars($job['JobRequirements'])) ?>
                        </div>

                        <h3 class="mb-4"><i class="fas fa-building"></i> About <?= htmlspecialchars($job['CompanyName']) ?></h3>
                        <p>Location: <?= htmlspecialchars($job['CompanyLocation']) ?></p>
                        <p>Contact: <?= htmlspecialchars($job['ContactPerson']) ?> (<?= htmlspecialchars($job['EmployerEmail']) ?>)</p>
                        <?php if ($job['EmployerPhone']): ?>
                            <p>Phone: <?= htmlspecialchars($job['EmployerPhone']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="job-actions">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Job Actions</h5>
                                <div class="d-grid gap-2">
                                    <?php if ($_SESSION['role'] == 'jobseeker'): ?>
                                        <?php if ($hasApplied): ?>
                                            <button class="btn btn-success" disabled>
                                                <i class="fas fa-check"></i> Already Applied
                                            </button>
                                        <?php else: ?>
                                            <a href="apply-job.php?job_id=<?= $jobId ?>" class="btn btn-primary">
                                                <i class="fas fa-paper-plane"></i> Apply Now
                                            </a>
                                        <?php endif; ?>

                                        
                                    <?php elseif ($_SESSION['role'] == 'employer' && $_SESSION['user_id'] == $job['EmployerId']): ?>
                                        <a href="edit-job.php?id=<?= $jobId ?>" class="btn btn-primary">
                                            <i class="fas fa-edit"></i> Edit Job
                                        </a>
                                        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                            <i class="fas fa-trash-alt"></i> Delete Job
                                        </button>
                                    <?php endif; ?>

                                    <a href="browse-jobs.php" class="btn btn-outline-primary">
                                        <i class="fas fa-arrow-left"></i> Back to Jobs
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Application Stats (for employer) -->
                        <?php if ($_SESSION['role'] == 'employer' && $_SESSION['user_id'] == $job['EmployerId']): ?>
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">Application Stats</h5>
                                    <?php
                                    $statsQuery = "SELECT Status, COUNT(*) as count 
                                              FROM Application 
                                              WHERE JobId = $jobId 
                                              GROUP BY Status";
                                    $statsResult = mysqli_query($conn, $statsQuery);
                                    $stats = [];
                                    while ($row = mysqli_fetch_assoc($statsResult)) {
                                        $stats[$row['Status']] = $row['count'];
                                    }
                                    ?>
                                    <ul class="list-group">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Total Applications
                                            <span class="badge bg-primary rounded-pill">
                                                <?= array_sum($stats) ?>
                                            </span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Shortlisted
                                            <span class="badge bg-success rounded-pill">
                                                <?= $stats['Shortlisted'] ?? 0 ?>
                                            </span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Rejected
                                            <span class="badge bg-danger rounded-pill">
                                                <?= $stats['Rejected'] ?? 0 ?>
                                            </span>
                                        </li>
                                    </ul>
                                    <a href="applications.php?job_id=<?= $jobId ?>" class="btn btn-sm btn-primary w-100 mt-3">
                                        View All Applications
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>

                        
        <!-- Delete Modal (for employer) -->
        <?php if ($_SESSION['role'] == 'employer' && $_SESSION['user_id'] == $job['EmployerId']): ?>
            <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm Delete</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete this job posting? This action cannot be undone.</p>
                            <p class="text-danger"><strong>All applications for this job will also be deleted.</strong></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <form action="delete-job.php" method="POST">
                                <input type="hidden" name="job_id" value="<?= $jobId ?>">
                                <button type="submit" class="btn btn-danger">Delete Job</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Save job functionality
            document.querySelector('.save-job-btn')?.addEventListener('click', function () {
                const jobId = this.getAttribute('data-job-id');
                const icon = this.querySelector('i');
                const isSaved = icon.classList.contains('fas');

                fetch('save-job.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `job_id=${jobId}&action=${isSaved ? 'unsave' : 'save'}`
                })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                if (isSaved) {
                                    icon.classList.replace('fas', 'far');
                                    this.innerHTML = '<i class="far fa-bookmark"></i> Save Job';
                                } else {
                                    icon.classList.replace('far', 'fas');
                                    this.innerHTML = '<i class="fas fa-bookmark"></i> Saved';
                                }
                            }
                        });
            });
        </script>
    </body>
</html>