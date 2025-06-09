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

// Get employer details
$employerId = $_SESSION['user_id'];
$query = "SELECT * FROM Employer WHERE Id = $employerId";
$result = mysqli_query($conn, $query);
$employer = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Employer Dashboard | sheroes</title>
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
                background-color: hotpink;
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
                background-color: hotpink;
            }
            .navbar-nav .nav-link i {
                margin-right: 8px;
            }
            .logout-btn {
                color: #ffffff;
                background-color:hotpink;
                border: none;
                padding: 0.75rem 1.5rem;
                border-radius: 8px;
                font-size: 1.1rem;
                transition: background-color 0.3s ease, transform 0.2s ease;
                margin-left: 1rem;
                font-weight: 500;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            }
            .logout-btn:hover {
                background-color:hotpink;
                transform: translateY(-2px);
                box-shadow: 0 3px 7px rgba(0, 0, 0, 0.15);
            }
            .main-content {
                padding: 2rem;
            }
            .welcome-header {
                background-color: #e0f7fa;
                color:hotpink;
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
                background-color: hotpink;
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
            }
            .dashboard-card-title {
                font-size: 1.5rem;
                color:white;
                margin-bottom: 1.5rem;
                display: flex;
                align-items: center;
                gap: 10px;
                font-weight: 600;
            }
            .dashboard-card-value {
                font-size: 2.5rem;
                font-weight: 700;
                color:hotpink;
                margin-bottom: 2rem;
            }
           .view-all-btn {
    background-color: white;
    color: hotpink;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-size: 1.1rem;
    transition: background-color 0.3s ease, transform 0.2s ease;
    border: none;
    font-weight: 500;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}
            .view-all-btn:hover {
                background-color:hotpink;
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
                color:hotpink;
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
                color:hotpink;
                font-size: 1.1rem;
            }
            .list-group-item small {
                color:hotpink;
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
                background-color:hotpink;
                color: #fff;
            }
            .badge-warning {
                background-color:hotpink;
                color: #343a40;
            }
            .badge-danger {
                background-color: hotpink;
                color: #fff;
            }
            .badge-primary {
                background-color: hotpink;
                color: #fff;
            }
            .badge-info {
                background-color:hotpink;
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

        <div class="main-content">
            <div class="welcome-header">
                <h1><i class="fas fa-building"></i> Welcome, <?= htmlspecialchars($employer['CompanyName']) ?></h1>
                <p>Employer Dashboard</p>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="dashboard-card">
                        <div class="dashboard-card-body">
                            <h5 class="dashboard-card-title"><i class="fas fa-briefcase text-primary"></i> Active Jobs</h5>
                            <?php
                            $query = "SELECT COUNT(*) as total FROM Job WHERE EmployerId = $employerId AND Status = 'Approved'";
                            $result = mysqli_query($conn, $query);
                            $row = mysqli_fetch_assoc($result);
                            ?>
                            <h2 class="dashboard-card-value"><?= $row['total'] ?></h2>
                            <a href="manage-posted-jobs.php" class="btn view-all-btn">View All</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="dashboard-card">
                        <div class="dashboard-card-body">
                            <h5 class="dashboard-card-title"><i class="fas fa-file-alt text-success"></i> Total Applications</h5>
                            <?php
                            $query = "SELECT COUNT(*) as total FROM Application a 
                                  JOIN Job j ON a.JobId = j.Id 
                                  WHERE j.EmployerId = $employerId";
                            $result = mysqli_query($conn, $query);
                            $row = mysqli_fetch_assoc($result);
                            ?>
                            <h2 class="dashboard-card-value"><?= $row['total'] ?></h2>
                            <a href="applications.php" class="btn view-all-btn">View All</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="dashboard-card">
                        <div class="dashboard-card-body">
                            <h5 class="dashboard-card-title"><i class="fas fa-clock text-warning"></i> Pending Jobs</h5>
                            <?php
                            $query = "SELECT COUNT(*) as total FROM Job WHERE EmployerId = $employerId AND Status = 'Pending'";
                            $result = mysqli_query($conn, $query);
                            $row = mysqli_fetch_assoc($result);
                            ?>
                            <h2 class="dashboard-card-value"><?= $row['total'] ?></h2>
                            <a href="manage-posted-jobs.php?status=Pending" class="btn view-all-btn">View</a>
                        </div>
                    </div>
                </div>
            </div>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
