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

// Get job seeker details
$jobSeekerId = $_SESSION['user_id'];
$query = "SELECT * FROM JobSeeker WHERE Id = $jobSeekerId";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Error fetching job seeker details: " . mysqli_error($conn));
}
$jobSeeker = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Seeker Dashboard | sheroes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f0f4f8;
            font-family: 'Inter', sans-serif;
            color: #333;
            padding-top: 70px;
        }

        .navbar {
            background-color: #ffffff;
            box-shadow: hotpink;
            padding: 1rem 0;
        }

 .navbar-brand {
    padding-top: var(--bs-navbar-brand-padding-y);
    padding-bottom: var(--bs-navbar-brand-padding-y);
    margin-right: var(--bs-navbar-brand-margin-end);
    font-size: var(--bs-navbar-brand-font-size);
    color:hotpink;
    text-decoration: none;
    white-space: nowrap;
}

       .navbar-nav .nav-link {
    color: hotpink;
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

        .main-content {
            padding: 2rem;
        }
        .navbar-nav .nav-link {
    color: hotpink;
    margin-right: 15px;
    font-weight: 500;
    transition: color 0.3s ease, transform 0.2s ease;
}

       .welcome-header {
   
    color: hotpink;
    padding: 2.5rem;
    border-radius: 12px;
    margin-bottom: 2.5rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    gap: 2rem;
}
        .welcome-header h1 {
            font-size: 2.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .welcome-header p {
            color: #666;
            font-size: 1.1rem;
        }
        i.fas.fa-users {
    color: hotpink;
}

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            padding: 2rem;
        }

       .card-title {
    font-size: 1.4rem;
    color: hotpink;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 600;
}
        .job-card {
            border-radius: 10px;
            margin-bottom: 1.2rem;
            background-color: #fff;
            box-shadow: 0 3px 9px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .job-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .job-card .list-group-item {
            border: none;
            padding: 1.5rem;
        }

        .job-card strong {
            color:rgb(196, 39, 159);
            font-size: 1.1rem;
        }

        .job-card small {
            color:rgb(213, 57, 208);
        }

        .job-card p {
            color: #555;
            line-height: 1.7;
            margin-top: 0.75rem;
        }

        .btn-primary {
    background-color: hotpink;
    border-color: white;
    color: white;
    padding: 0.8rem 1.8rem;
    font-weight: 500;
    transition: background-color 0.3s ease, border-color 0.3s ease, transform 0.2s ease;
}
.btn-info {
    --bs-btn-color: white;
    --bs-btn-bg: hotpink;
    --bs-btn-border-color: hotpink;
    --bs-btn-hover-color: #000;
    --bs-btn-hover-bg: hotpink;
    --bs-btn-hover-border-color:hotpink;
    --bs-btn-focus-shadow-rgb: 11,172,204;
    --bs-btn-active-color: #000;
    --bs-btn-active-bg: hotpink;
    --bs-btn-active-border-color: hotpink;
    --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
    --bs-btn-disabled-color: #000;
    --bs-btn-disabled-bg: hotpink;
    --bs-btn-disabled-border-color: hotpink;
}.h5, h5 {
    font-size: 1.25rem;
    color: hotpink;
}.text-warning {
    --bs-text-opacity: 1;
    color: hotpink;
}a {
    color: hotpink;
    text-decoration: underline;
}

        .btn-primary:hover {
            background-color:rgb(101, 8, 90);
            border-color:hotpink;
            transform: translateY(-2px);
        }

        .btn-outline-primary {
            color:rgb(230, 19, 139);
            border-color:rgb(190, 14, 158);
            padding: 0.8rem 1.8rem;
            font-weight: 500;
            transition: color 0.3s ease, border-color 0.3s ease, transform 0.2s ease;
        }

        .btn-outline-primary:hover {
            background-color: #f0f8fa;
            color:rgb(170, 44, 90);
            border-color:rgb(209, 17, 148);
            transform: translateY(-2px);
        }
 
.btn-success {
    --bs-btn-color: #fff;
    --bs-btn-bg: hotpink;
    --bs-btn-border-color: hotpink;
    --bs-btn-hover-color: #fff;
    --bs-btn-hover-bg: hotpink;
    --bs-btn-hover-border-color: hotpink;
    --bs-btn-focus-shadow-rgb: 60,153,110;
    --bs-btn-active-color: #fff;
    --bs-btn-active-bg: hotpink;
    --bs-btn-active-border-color: hotpink;
    --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
    --bs-btn-disabled-color: #fff;
    --bs-btn-disabled-bg: hotpink;
    --bs-btn-disabled-border-color: hotpink;
}.bg-primary {
    --bs-bg-opacity: 1;
    background-color: hotpink !important;
}
        .badge {
            border-radius: 8px;
            font-weight: 500;
            padding: 0.5rem 0.8rem;
            font-size: 0.9rem;
        }

        .list-group-item {
            border-radius: 8px;
            margin-bottom: 0.75rem;
            background-color: #fff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        }
        .icon-lg{
          font-size: 2rem;
        }

    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="jobseeker-dashboard.php">
                <i class="fas fa-users"></i> SHEROES
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="jobseeker-dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="browse-jobs.php">
                            <i class="fas fa-search"></i> Browse Jobs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="my-applications.php">
                            <i class="fas fa-file-alt"></i> My Applications
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">
                            <i class="fas fa-user"></i> My Profile
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

    <div class="container-fluid">
        <div class="row">
            <div class="col-12 main-content">
                <div class="welcome-header">
                    <i class="fas fa-user-tie icon-lg text-cyan"></i>
                    <div>
                        <h1>Welcome, <?php echo $jobSeeker['Name']; ?></h1>
                        <p class="mb-0">Your Job Seeker Dashboard</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-file-alt text-indigo"></i> Applications
                                </h5>
                                <?php
                                $query = "SELECT COUNT(*) as total FROM Application WHERE JobSeekerId = $jobSeekerId";
                                $result = mysqli_query($conn, $query);
                                if (!$result) {
                                    die("Error fetching application count: " . mysqli_error($conn));
                                }
                                $row = mysqli_fetch_assoc($result);
                                ?>
                                <h2><?php echo $row['total']; ?></h2>
                                <a href="my-applications.php" class="btn btn-primary">View All</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-check-circle text-success"></i> Shortlisted
                                </h5>
                                <?php
                                $query = "SELECT COUNT(*) as total FROM Application WHERE JobSeekerId = $jobSeekerId AND Status = 'Shortlisted'";
                                $result = mysqli_query($conn, $query);
                                if (!$result) {
                                    die("Error fetching shortlisted count: " . mysqli_error($conn));
                                }
                                $row = mysqli_fetch_assoc($result);
                                ?>
                                <h2><?php echo $row['total']; ?></h2>
                                <a href="my-applications.php?status=Shortlisted" class="btn btn-success">View</a>
                            </div>
                        </div>
                    </div>
                   
                

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
