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
$statusFilter = $_GET['status'] ?? '';

// Handle application status update
if (isset($_POST['update_status'])) {
    $applicationId = $_POST['application_id'];
    $newStatus = $_POST['status'];

    $query = "UPDATE Application SET Status = ? WHERE Id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'si', $newStatus, $applicationId);
    mysqli_stmt_execute($stmt);

    $_SESSION['message'] = "Application status updated successfully";
    header("Location: applications.php");
    exit();
}

// Get applications with optional status filter
$query = "SELECT a.*, j.JobTitle, js.Name, js.Email, js.Phone, js.ResumeFile
            FROM Application a
            JOIN Job j ON a.JobId = j.Id
            JOIN JobSeeker js ON a.JobSeekerId = js.Id
            WHERE j.EmployerId = $employerId";

if ($statusFilter) {
    $query .= " AND a.Status = '$statusFilter'";
}

$query .= " ORDER BY a.ApplicationDate DESC";
$result = mysqli_query($conn, $query);
$applications = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Manage Applications | sheroes</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <style>
            body {
                font-family: 'Inter', sans-serif;
                background-color: #f0f4f8;
                color: #333;
                line-height: 1.7;
            }
            nav {
                background-color:HOTPINK;
                color: #fff;
                padding: 1rem 2rem;
                border-radius: 0;
            }
            nav .navbar-brand {
                color: #fff;
                font-size: 1.8rem;
                font-weight: 600;
            }
            nav .navbar-nav .nav-item .nav-link {
                color: rgba(255, 255, 255, 0.8);
                margin-left: 1rem;
                padding: 0.5rem 1rem;
                border-radius: 8px;
                transition: all 0.3s ease;
            }
            nav .navbar-nav .nav-item .nav-link:hover,
            nav .navbar-nav .nav-item .nav-link.active {
                color: #fff;
                background-color: rgba(255, 255, 255, 0.1);
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            }
            main {
                padding: 2rem;
            }
            .card {
                border-radius: 12px;
                box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
                margin-bottom: 30px;
                border: none;
            }
            .card-header {
                background-color: #fff;
                padding: 25px;
                border-bottom: 2px solid #e0e0e0;
                border-top-left-radius: 12px;
                border-top-right-radius: 12px;
            }
            .card-header h1 {
                font-size: 2rem;
                color:HOTPINK;
                margin-bottom: 0;
                display: flex;
                align-items: center;
                gap: 10px;
                font-weight: 600;
            }
            .btn-primary {
                background-color: HOTPINK;
                border: none;
                padding: 14px 32px;
                border-radius: 8px;
                font-weight: 600;
                font-size: 1.2rem;
                transition: all 0.3s ease;
                box-shadow: 0 5px 11px rgba(0, 0, 0, 0.1);
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .btn-primary:hover {
                background-color:HOTPINK;
                transform: translateY(-2px);
                box-shadow: 0 7px 15px rgba(0, 0, 0, 0.15);
            }
            .btn-outline-primary {
                color:HOTPINK;
                border-color:HOTPINK;
            }
            .btn-outline-primary:hover {
                background-color: #f0f4f8;
                color: HOTPINK;
            }
            .btn-outline-info {
                color:HOTPINK;
                border-color: HOTPINK;
            }
            .btn-outline-info:hover {
                background-color: #f0f4f8;
                color: HOTPINK;
            }
            .btn-outline-success {
                color: HOTPINK;
                border-color:HOTPINK;
            }
            .btn-outline-success:hover {
                background-color: #f0fdf4;
                color:HOTPINK;
            }
            .btn-outline-danger {
                color:HOTPINK;
                border-color:HOTPINK;
            }
            .btn-outline-danger:hover {
                background-color: #fef0f0;
                color:HOTPINK;
            }
            .badge-applied {
                background-color:HOTPINK;
                color: #fff;
            }
            .badge-reviewed {
                background-color:hotpink;
                color: #fff;
            }
            .badge-shortlisted {
                background-color:hotpink;
                color: #fff;
            }
            .badge-rejected {
                background-color:hotpink;
                color: #fff;
            }
            .filter-buttons .btn {
                margin-right: 5px;
                margin-bottom: 5px;
            }
            .application-card {
                border-radius: 12px;
                margin-bottom: 25px;
                background-color: #fff;
                box-shadow: 0 5px 11px rgba(0, 0, 0, 0.08);
                transition: all 0.3s ease;
                border-left: 4px solid;
            }
            .application-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 7px 15px rgba(0, 0, 0, 0.15);
            }
            .application-card.applied {
                border-left-color:hotpink;
            }
            .application-card.reviewed {
                border-left-color:hotpink;
            }
            .application-card.shortlisted {
                border-left-color: hotpink;
            }
            .application-card.rejected {
                border-left-color: hotpink;
            }
            .application-card h5 {
                font-size: 1.4rem;
                color: #1a202c;
                margin-bottom: 0.5rem;
                font-weight: 600;
            }
            .application-card h6 {
                font-size: 1.1rem;
                color:hotpink8;
                margin-bottom: 0.25rem;
                font-weight: 500;
            }
            .application-card small {
                color:hotpink;
            }
            .application-card p {
                margin-bottom: 0.5rem;
            }
            .application-card .badge {
                padding: 0.5rem 1rem;
                border-radius: 12px;
                font-size: 0.9rem;
                font-weight: 600;
                min-width: 100px;
                text-align: center;
            }
            .application-card .badge-applied {
                background-color:hotpink;
                color: #fff;
            }
            .application-card .badge-reviewed {
                background-color:hotpink;
                color: #fff;
            }
            .application-card .badge-shortlisted {
                background-color:hotpink;
                color: #fff;
            }
            .application-card .badge-rejected {
                background-color:hotpink;
                color: #fff;
            }
            .application-card .btn {
                padding: 0.75rem 1.5rem;
                font-size: 1rem;
                border-radius: 8px;
                font-weight: 600;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .application-card .btn-outline-primary {
                color:hotpink;
                border-color:hotpink;
            }
            .application-card .btn-outline-primary:hover {
                background-color: #f0f4f8;
                color:hotpink;
            }
            .application-card form {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-top: 1rem;
            }
            .application-card form .form-select {
                width: auto;
                flex: 1;
                border-radius: 8px;
                font-size: 0.9rem;
                padding: 0.5rem;
                border-color: #e0e0e0;
            }
            .application-card form .btn-primary {
                padding: 0.5rem 1.5rem;
                font-size: 0.9rem;
                font-weight: 600;
                border-radius: 8px;
                transition: all 0.3s ease;
            }
            .application-card form .btn-primary:hover {
                background-color:hotpink;
                transform: translateY(-2px);
                box-shadow: 0 3px 7px rgba(0, 0, 0, 0.1);
            }.alert-info {
    --bs-alert-color: WHITE;
    --bs-alert-bg: HOTPINK;
    --bs-alert-border-color: HOTPINK;
    --bs-alert-link-color: HOTPINK;
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
                    <h1><i class="fas fa-file-alt"></i> Job Applications</h1>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['message'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['message'];
                            unset($_SESSION['message']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
<?php endif; ?>

                    <div class="filter-buttons mb-4">
                        <a href="applications.php" class="btn btn-outline-primary btn-lg">All</a>
                        <a href="applications.php?status=Applied" class="btn btn-outline-primary btn-lg">Applied</a>
                        <a href="applications.php?status=Reviewed" class="btn btn-outline-info btn-lg">Reviewed</a>
                        <a href="applications.php?status=Shortlisted" class="btn btn-outline-success btn-lg">Shortlisted</a>
                        <a href="applications.php?status=Rejected" class="btn btn-outline-danger btn-lg">Rejected</a>
                    </div>

<?php if (empty($applications)): ?>
                        <div class="alert alert-info">
                            No applications found.
                        </div>
                        <?php else: ?>
                        <div class="row">
    <?php foreach ($applications as $app): ?>
                                <div class="col-md-6">
                                    <div class="application-card <?php echo strtolower($app['Status']); ?>">
                                        <h5><?php echo htmlspecialchars($app['JobTitle']); ?></h5>
                                        <h6><?php echo htmlspecialchars($app['Name']); ?></h6>
                                        <small>Applied on: <?php echo date('M d, Y', strtotime($app['ApplicationDate'])); ?></small>
                                        <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($app['Email']); ?></p>
                                        <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($app['Phone'] ?? 'Not provided'); ?></p>
        <?php if ($app['ResumeFile']): ?>
                                            <div>
                                                <a href="uploads/resumes/<?php echo htmlspecialchars($app['ResumeFile']); ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="fas fa-download"></i> Download Resume
                                                </a>
                                            </div>
        <?php endif; ?>
                                        <form method="POST">
                                            <input type="hidden" name="application_id" value="<?php echo $app['Id']; ?>">
                                            <select name="status" class="form-select">
                                                <option value="Applied" <?php echo $app['Status'] == 'Applied' ? 'selected' : ''; ?>>Applied</option>
                                                <option value="Reviewed" <?php echo $app['Status'] == 'Reviewed' ? 'selected' : ''; ?>>Reviewed</option>
                                                <option value="Shortlisted" <?php echo $app['Status'] == 'Shortlisted' ? 'selected' : ''; ?>>Shortlisted</option>
                                                <option value="Rejected" <?php echo $app['Status'] == 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                                            </select>
                                            <button type="submit" name="update_status" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Update Status
                                            </button>
                                        </form>
                                        <span class="badge badge-<?php echo strtolower($app['Status']); ?>">
        <?php echo $app['Status']; ?>
                                        </span>
                                    </div>
                                </div>
                        <?php endforeach; ?>
                        </div>
<?php endif; ?>
                </div>
            </div>
        </main>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
