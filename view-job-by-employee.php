<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';

$jobId = $_GET['id'] ?? 0;
$employerId = $_SESSION['role'] == 'employer' ? $_SESSION['user_id'] : 0;

// Get job details
$query = "SELECT j.*, c.CategoryName, e.CompanyName, e.ContactPerson, e.Email as CompanyEmail, e.Phone as CompanyPhone
          FROM Job j
          LEFT JOIN JobCategory c ON j.CategoryId = c.Id
          LEFT JOIN Employer e ON j.EmployerId = e.Id
          WHERE j.Id = ?";

if ($_SESSION['role'] == 'employer') {
    $query .= " AND j.EmployerId = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ii', $jobId, $employerId);
} else {
    $query .= " AND j.Status = 'Approved'";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $jobId);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$job = mysqli_fetch_assoc($result);

if (!$job) {
    header("Location: not-found.php");
    exit();
}

// Get applications count if employer
$applicationsCount = 0;
if ($_SESSION['role'] == 'employer') {
    $query = "SELECT COUNT(*) as count FROM Application WHERE JobId = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $jobId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $applicationsCount = $row['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($job['JobTitle']); ?> | sheroes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .btn-primary {
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
    --bs-btn-disabled-bg: HOTPINK;
    --bs-btn-disabled-border-color: HOTPINK;
}
            .sidebar {
                background-color:hotpink;
                min-height: 100vh;
                color: white;
            }
            .sidebar .nav-link {
                color: rgba(255, 255, 255, 0.8);
                margin-bottom: 5px;
            }
            .sidebar .nav-link:hover, .sidebar .nav-link.active {
                color: white;
                background-color: HOTPINK;
            }
            .sidebar .nav-link i {
                margin-right: 10px;
            }
        .main-content {
            padding: 20px;
        }
        .job-header {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 25px;
            margin-bottom: 20px;
        }
        .job-details-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 25px;
            margin-bottom: 20px;
        }
        .company-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 25px;
        }
        .badge-status {
            font-size: 0.9rem;
            padding: 8px 12px;
        }
        .job-type-badge {
            font-size: 0.9rem;
            padding: 8px 12px;
            background-color:hotpink;
        }
        .detail-icon {
            width: 30px;
            text-align: center;
            color: HOTPINK;
        }
        .detail-item {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'sidebar.php'; ?>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>Job Details</h1>
                    <div>
                        <?php if ($_SESSION['role'] == 'employer'): ?>
                            <a href="manage-posted-jobs.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Jobs
                            </a>
                        <a href="edit-job-by-employee.php?id=<?php echo $jobId; ?>" class="btn btn-primary ms-2">
                                <i class="fas fa-edit"></i> Edit Job
                            </a>
                        <?php else: ?>
                            <a href="jobs.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Jobs
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="job-header">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h2><?php echo htmlspecialchars($job['JobTitle']); ?></h2>
                            <h4 class="text-muted"><?php echo htmlspecialchars($job['CompanyName']); ?></h4>
                        </div>
                        <div class="text-end">
                            <span class="badge rounded-pill job-type-badge"><?php echo $job['JobType']; ?></span>
                            <?php if ($_SESSION['role'] == 'employer'): ?>
                                <span class="badge rounded-pill 
                                    <?php echo $job['Status'] == 'Pending' ? 'bg-warning' : 
                                          ($job['Status'] == 'Approved' ? 'bg-success' : 'bg-danger'); ?> badge-status">
                                    <?php echo $job['Status']; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="text-muted"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($job['Location']); ?></span>
                        <?php if ($job['SalaryRange']): ?>
                            <span class="text-muted ms-3"><i class="fas fa-money-bill-wave"></i> <?php echo htmlspecialchars($job['SalaryRange']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="job-details-card">
                            <h4 class="mb-4"><i class="fas fa-info-circle"></i> Job Description</h4>
                            <div class="mb-4">
                                <?php echo nl2br(htmlspecialchars($job['JobRequirements'])); ?>
                            </div>

                            <h4 class="mb-4"><i class="fas fa-calendar-alt"></i> Important Dates</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <span class="detail-icon"><i class="fas fa-paper-plane"></i></span>
                                        <strong>Posted:</strong> <?php echo date('M d, Y', strtotime($job['CreatedAt'])); ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <span class="detail-icon"><i class="fas fa-clock"></i></span>
                                        <strong>Deadline:</strong> <?php echo date('M d, Y', strtotime($job['ApplicationDeadline'])); ?>
                                    </div>
                                </div>
                            </div>

                            <h4 class="mt-4 mb-4"><i class="fas fa-tag"></i> Job Details</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <span class="detail-icon"><i class="fas fa-briefcase"></i></span>
                                        <strong>Job Type:</strong> <?php echo $job['JobType']; ?>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-icon"><i class="fas fa-layer-group"></i></span>
                                        <strong>Category:</strong> <?php echo htmlspecialchars($job['CategoryName']); ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <?php if ($_SESSION['role'] == 'employer'): ?>
                                        <div class="detail-item">
                                            <span class="detail-icon"><i class="fas fa-file-alt"></i></span>
                                            <strong>Applications:</strong> <?php echo $applicationsCount; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="company-card">
                            <h4 class="mb-4"><i class="fas fa-building"></i> About Company</h4>
                            <div class="mb-3">
                                <h5><?php echo htmlspecialchars($job['CompanyName']); ?></h5>
                            </div>
                            <div class="mb-3">
                                <strong>Contact Person:</strong> <?php echo htmlspecialchars($job['ContactPerson']); ?>
                            </div>
                            <div class="mb-3">
                                <strong>Email:</strong> <?php echo htmlspecialchars($job['CompanyEmail']); ?>
                            </div>
                            <div class="mb-3">
                                <strong>Phone:</strong> <?php echo htmlspecialchars($job['CompanyPhone']); ?>
                            </div>
                            <div class="mb-3">
                                <strong>Location:</strong> <?php echo htmlspecialchars($job['Location']); ?>
                            </div>

                            <?php if ($_SESSION['role'] == 'jobseeker'): ?>
                                <hr>
                                <div class="d-grid gap-2">
                                    <a href="apply-job.php?id=<?php echo $jobId; ?>" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i> Apply Now
                                    </a>
                                    <a href="save-job.php?id=<?php echo $jobId; ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-bookmark"></i> Save Job
                                    </a>
                                </div>
                            <?php elseif ($_SESSION['role'] == 'employer'): ?>
                                <hr>
                                <div class="d-grid gap-2">
                                    <a href="applications.php?job_id=<?php echo $jobId; ?>" class="btn btn-primary">
                                        <i class="fas fa-file-alt"></i> View Applications (<?php echo $applicationsCount; ?>)
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>