<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
if ($_SESSION['role'] != 'admin') {
    header("Location: unauthorized.php");
    exit();
}
include 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Dashboard | Sheroes</title>
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
            }
            nav {
                background-color:HOTPINK;
                color: #fff;
                padding: 1rem 2rem;
                border-radius: 0;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            }
            nav .navbar-brand {
                color: #fff;
                font-size: 1.8rem;
                font-weight: 600;
            }
            nav .navbar-nav .nav-item .nav-link {
                color: rgba(255, 255, 255, 0.8);
                margin-left: 1rem;
                padding: 0.75rem 1.5rem;
                border-radius: 8px;
                transition: all 0.3s ease;
            }
            nav .navbar-nav .nav-item .nav-link:hover,
            nav .navbar-nav .nav-item .nav-link.active {
                color: #fff;
                background-color:HOTPINK;
                box-shadow:HOTPINK;
            }
            main {
                padding: 2rem;
            }
            .welcome-header {
                background-color:HOTPINK;
                color: white;
                padding: 2rem;
                border-radius: 12px;
                margin-bottom: 2rem;
                background-image: linear-gradient(to right,HOTPINK,HOTPINK);
                box-shadow: 0 5px 11px rgba(0, 0, 0, 0.1);
            }
            .welcome-header h1 {
                font-size: 2.2rem;
                font-weight: 600;
                margin-bottom: 1rem;
                display: flex;
                align-items: center;
                gap: 15px;
            }
            .welcome-header p {
                font-size: 1.1rem;
                color: rgba(255, 255, 255, 0.9);
                margin-bottom: 0;
            }
            .card {
                margin-bottom: 2rem;
                border-radius: 12px;
                box-shadow: HOTPINK;
                border: none;
                transition: all 0.3s ease;
            }
            .card:hover{
                transform: translateY(-5px);
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            }
            .card-body {
                padding: 2rem;
            }
            .card-title {
                font-size: 1.4rem;
                color:HOTPINK;
                margin-bottom: 1.5rem;
                display: flex;
                align-items: center;
                gap: 10px;
                font-weight: 600;
            }
            .card-body h2 {
                font-size: 2rem;
                font-weight: 700;
                color:HOTPINK;
                margin-bottom: 1rem;
            }
            .btn-sm {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
                border-radius: 8px;
                transition: all 0.3s ease;
            }
            .btn-primary {
                background-color:HOTPINK;
                border-color:HOTPINK;
                color: #fff;
            }
            .btn-primary:hover {
                background-color:HOTPINK;
                border-color:HOTPINK;
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }
            .btn-success {
                background-color:HOTPINK;
                border-color:HOTPINK;
                color: #fff;
            }
            .btn-success:hover {
                background-color:HOTPINK;
                border-color: HOTPINK;
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }
            .btn-info {
                background-color:HOTPINK;
                border-color:HOTPINK;
                color: #fff;
            }
            .btn-info:hover {
                background-color:HOTPINK;
                border-color: HOTPINK;
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }
            .list-group-item {
                border-radius: 8px;
                margin-bottom: 0.5rem;
                border: 1px solid #e0e0e0;
                transition: all 0.3s ease;
            }
            .list-group-item:hover{
                background-color: #f8f8f8;
                transform: translateX(5px);
                box-shadow:HOTPINK;
            }
            .badge {
                border-radius: 12px;
                font-size: 0.9rem;
                padding: 0.5rem 1rem;
            }
            .text-muted {
                font-size: 0.9rem;
                color: #999;
            }
            .mt-4{
                margin-top: 2.5rem !important;
            }
        </style>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">SHEROES</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link active" href="admin-dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage-users.php">
                                <i class="fas fa-users"></i> Manage Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage-jobs.php">
                                <i class="fas fa-briefcase"></i> Manage Jobs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-danger" href="logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <main class="container">
            <div class="welcome-header">
                <h1><i class="fas fa-user-shield"></i> Welcome, <?php echo $_SESSION['name']; ?></h1>
                <p class="mb-0">Admin Dashboard</p>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-users text-primary"></i> Total Users</h5>
                            <?php
                            $query = "SELECT COUNT(*) as total FROM (SELECT Id FROM JobSeeker UNION ALL SELECT Id FROM Employer) as users";
                            $result = mysqli_query($conn, $query);
                            $row = mysqli_fetch_assoc($result);
                            ?>
                            <h2><?php echo $row['total']; ?></h2>
                            <a href="manage-users.php" class="btn btn-sm btn-primary">View All</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-briefcase text-success"></i> Total Jobs</h5>
                            <?php
                            $query = "SELECT COUNT(*) as total FROM Job";
                            $result = mysqli_query($conn, $query);
                            $row = mysqli_fetch_assoc($result);
                            ?>
                            <h2><?php echo $row['total']; ?></h2>
                            <a href="manage-jobs.php" class="btn btn-sm btn-success">View All</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-file-alt text-info"></i> Pending Jobs</h5>
                            <?php
                            $query = "SELECT COUNT(*) as total FROM Job WHERE Status = 'Pending'";
                            $result = mysqli_query($conn, $query);
                            $row = mysqli_fetch_assoc($result);
                            ?>
                            <h2><?php echo $row['total']; ?></h2>
                            <a href="manage-jobs.php?status=Pending" class="btn btn-sm btn-info">Review</a>
                        </div>
                    </div>
                </div>
            </div>

           
        </main>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
