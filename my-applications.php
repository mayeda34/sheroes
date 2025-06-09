<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
if ($_SESSION['role'] != 'jobseeker') {
    header("Location: unauthorized.php");
    exit();
}
include 'config.php';

$jobSeekerId = $_SESSION['user_id'];
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';

$query = "SELECT a.*, j.JobTitle, j.CompanyName, j.Location, e.Email as EmployerEmail
          FROM Application a
          JOIN Job j ON a.JobId = j.Id
          JOIN Employer e ON j.EmployerId = e.Id
          WHERE a.JobSeekerId = $jobSeekerId";

if (!empty($statusFilter)) {
    $query .= " AND a.Status = ?";
}

$query .= " ORDER BY a.ApplicationDate DESC";

$stmt = mysqli_prepare($conn, $query);
if ($stmt === false) {
    die("Error preparing statement: " . mysqli_error($conn));
}

if (!empty($statusFilter)) {
    mysqli_stmt_bind_param($stmt, "s", $statusFilter);
}

$result = mysqli_stmt_execute($stmt);
if ($result === false) {
    die("Error executing statement: " . mysqli_stmt_error($stmt));
}
$applications = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>My Applications | sheroes</title>
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

            .status-badge {
                font-size: 0.85rem;
                padding: 6px 12px;
                border-radius: 16px;
                font-weight: 500;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            }

            .status-badge.bg-primary {
                background-color:hotpink;
                color: white;
            }

            .status-badge.bg-info {
                background-color:hotpink;
                color: white;
            }

            .status-badge.bg-success {
                background-color:hotpink;
                color: white;
            }

            .status-badge.bg-danger {
                background-color:hotpink;
                color: white;
            }

            .application-card {
                transition: all 0.3s ease;
                margin-bottom: 20px;
                border-radius: 12px;
                background-color: white;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
                border: none;
            }

            .application-card:hover {
                box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
                transform: translateY(-4px);
            }

            .application-card-body {
                padding: 20px;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                height: 100%;
            }

            .job-title {
                font-size: 1.25rem;
                color:hotpink;
                font-weight: 600;
                margin-bottom: 8px;
            }

            .company-name {
                font-size: 1rem;
                color:hotpink;
                margin-bottom: 12px;
            }

            .location {
                font-size: 0.9rem;
                color: #555;
                margin-bottom: 4px;
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .email {
                font-size: 0.9rem;
                color: #555;
                margin-bottom: 0;
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .view-job-btn {
                background-color:hotpink;
                color: white;
                border-radius: 8px;
                padding: 10px 16px;
                font-weight: 500;
                transition: background-color 0.3s ease, transform 0.2s ease;
                border: none;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
            }

            .view-job-btn:hover {
                background-color:hotpink;
                transform: translateY(-2px);
            }

            .contact-employer-btn {
                background-color:hotpink;
                color: white;
                border-radius: 8px;
                padding: 10px 16px;
                font-weight: 500;
                transition: background-color 0.3s ease, transform 0.2s ease;
                border: none;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
            }

            .contact-employer-btn:hover {
                background-color:hotpink;
                transform: translateY(-2px);
            }

            .no-applications-message {
                background-color: #e3f2fd;
                color:hotpink;
                padding: 20px;
                border-radius: 12px;
                margin-bottom: 20px;
                text-align: center;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            }

            .browse-jobs-btn {
                background-color:hotpink;
                color: white;
                border-radius: 8px;
                padding: 12px 24px;
                font-weight: 600;
                transition: background-color 0.3s ease, transform 0.2s ease;
                border: none;
                box-shadow: 0 3px 7px rgba(0, 0, 0, 0.1);
            }

            .browse-jobs-btn:hover {
                background-color: hotpink;
                transform: translateY(-2px);
            }

            .filter-group .btn {
                border-radius: 8px;
                margin-right: 8px;
                padding: 10px 18px;
                font-weight: 500;
                transition: all 0.3s ease;
                border: none;
                box-shadow: none;
            }

            .filter-group .btn:hover {
                background-color: #e0f7fa;
                color: hotpink;
                transform: translateY(-2px);
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
            }

            .filter-group .btn.active {
                background-color:hotpink;
                color: white;
                box-shadow: 0 3px 7px rgba(0, 0, 0, 0.1);
            }

            .modal-header {
                background-color: #f0f8fa;
                border-bottom: 1px solid #e0e0e0;
                padding: 20px;
                border-top-left-radius: 12px;
                border-top-right-radius: 12px;
            }

            .modal-title {
                font-size: 1.2rem;
                color:hotpink;
                font-weight: 600;
            }

            .modal-body {
                padding: 20px;
                font-size: 1rem;
                color: #555;
            }

            .modal-footer {
                border-top: none;
                padding: 20px;
                display: flex;
                justify-content: flex-end;
                border-bottom-left-radius: 12px;
                border-bottom-right-radius: 12px;
            }

            .modal-footer .btn-secondary {
                background-color: #e0e0e0;
                color: #333;
                border-radius: 8px;
                padding: 10px 18px;
                font-weight: 500;
                transition: all 0.3s ease;
                border: none;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
            }

            .modal-footer .btn-secondary:hover {
                background-color: #d0d0d0;
                transform: translateY(-2px);
                box-shadow: 0 3px 7px rgba(0, 0, 0, 0.1);
            }

            .navbar {
                background-color: #ffffff;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                padding: 1rem 0;
            }
.bg-primary {
    --bs-bg-opacity: 1;
    background-color: hotpink !important;
}
            .navbar-brand {
                color:hotpink;
                font-weight: 600;
                font-size: 1.8rem;
            }

           .navbar-nav .nav-link {
    color: HOTPINK;
    margin-right: 15px;
    font-weight: 500;
    transition: color 0.3s ease, transform 0.2s ease;
}

            .navbar-nav .nav-link:hover,
            .navbar-nav .nav-link.active {
                color:hotpink;
                transform: translateY(-2px);
            }

            .navbar-nav .nav-link i {
                margin-right: 8px;
            }
        </style>
    </head>
    <body>
        <?php include 'includes/jobseeker-navbar.php'; ?>

        <div class="container py-5">
            <div class="row">
                <div class="col-lg-12">
                    <h2 class="mb-4"><i class="fas fa-file-alt"></i> My Applications</h2>

                    <div class="mb-4 filter-group">
                        <div class="btn-group" role="group">
                            <a href="my-applications.php" class="btn btn-outline-secondary <?= empty($statusFilter) ? 'active' : '' ?>">All</a>
                            <a href="my-applications.php?status=Applied" class="btn btn-outline-primary <?= $statusFilter == 'Applied' ? 'active' : '' ?>">Applied</a>
                            <a href="my-applications.php?status=Reviewed" class="btn btn-outline-info <?= $statusFilter == 'Reviewed' ? 'active' : '' ?>">Reviewed</a>
                            <a href="my-applications.php?status=Shortlisted" class="btn btn-outline-success <?= $statusFilter == 'Shortlisted' ? 'active' : '' ?>">Shortlisted</a>
                            <a href="my-applications.php?status=Rejected" class="btn btn-outline-danger <?= $statusFilter == 'Rejected' ? 'active' : '' ?>">Rejected</a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <?php if (mysqli_num_rows($applications) > 0): ?>
                                <?php while ($app = mysqli_fetch_assoc($applications)): ?>
                                    <div class="card application-card">
                                        <div class="card-body application-card-body">
                                            <div class="application-card-content">
                                                <h5 class="job-title"><?= htmlspecialchars($app['JobTitle']) ?></h5>
                                                <h6 class="company-name"><?= htmlspecialchars($app['CompanyName']) ?></h6>
                                                <p class="location"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($app['Location']) ?></p>
                                                <p class="email"><i class="fas fa-envelope"></i> <?= htmlspecialchars($app['EmployerEmail']) ?></p>
                                            </div>
                                            <div class="application-card-status">
                                                <?php
                                                $badgeClass = [
                                                    'Applied' => 'bg-primary',
                                                    'Reviewed' => 'bg-info',
                                                    'Shortlisted' => 'bg-success',
                                                    'Rejected' => 'bg-danger'
                                                        ][$app['Status']];
                                                ?>
                                                <span class="badge status-badge <?= $badgeClass ?>"><?= htmlspecialchars($app['Status']) ?></span>
                                                <p class="text-muted small mt-2">Applied: <?= date('M d, Y', strtotime($app['ApplicationDate'])) ?></p>
                                            </div>
                                            <div class="application-card-actions d-flex justify-content-between align-items-center mt-3">
                                                <a href="job-details.php?id=<?= $app['JobId'] ?>" class="btn view-job-btn">View Job</a>
                                                <?php if ($app['Status'] == 'Shortlisted'): ?>
                                                    <button class="btn contact-employer-btn" data-bs-toggle="modal" data-bs-target="#contactModal">Contact Employer</button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="alert alert-info no-applications-message">You haven't applied to any jobs yet.</div>
                                <a href="browse-jobs.php" class="btn browse-jobs-btn">Browse Jobs</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="contactModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Contact Employer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>You've been shortlisted for this position! You can contact the employer directly at the email provided.</p>
                        <p>Remember to be professional in your communication.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
