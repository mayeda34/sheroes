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

if (isset($_GET['id'])) {
    $jobId = intval($_GET['id']);

    // Fetch job details
    $query = "SELECT j.Id, j.JobTitle, j.CompanyName, j.Location, j.JobRequirements, j.SalaryRange, j.JobType, j.ApplicationDeadline, j.Status, j.CreatedAt, c.CategoryName  
                FROM Job j
                JOIN JobCategory c ON j.CategoryId = c.Id
                WHERE j.Id = $jobId";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $job = mysqli_fetch_assoc($result);
    } else {
        header("Location: manage-jobs.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Job | Career Hub</title>
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
            background-color:hotpink;
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
            background-color: hotpink;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        main {
            padding: 2rem;
        }
        .card {
            margin-bottom: 2rem;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
            border: none;
        }
        .card-header {
            background-color: #fff;
            padding: 1.5rem;
            border-bottom: 1px solid #e0e0e0;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }
        .card-header h2{
            color:hotpink;
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 0;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .card-body {
            padding: 2rem;
        }
        .card-body p {
            line-height: 1.8;
            color: #444;
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
        }
        .card-body p strong{
            color:hotpink;
        }
        .card-footer {
            background-color: #fff;
            padding: 1.5rem;
            border-top: 1px solid #e0e0e0;
            border-bottom-left-radius: 12px;
            border-bottom-right-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-secondary {
            background-color:hotpink;
            border-color:hotpink;
            color: #fff;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 1rem;
        }
        .btn-secondary:hover {
            background-color:hotpink;
            border-color: hotpink;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .btn-warning {
            background-color: hotpink;
            border-color:hotpink;
            color: #fff;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 1rem;
        }
        .btn-warning:hover {
            background-color:hotpink;
            border-color: hotpink;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .badge {
            border-radius: 12px;
            font-size: 1rem;
            padding: 0.5rem 1rem;
        }
        .badge-success {
            background-color:hotpink;
            color: #fff;
        }
        .badge-warning {
            background-color:hotpink;
            color: #fff;
        }
        .badge-danger {
            background-color:hotpink;
            color: #fff;
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
                        <a class="nav-link" href="admin-dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage-users.php">
                            <i class="fas fa-users"></i> Manage Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="manage-jobs.php">
                            <i class="fas fa-briefcase"></i> Manage Jobs
                        </a>
                    </li>
                     <li class="nav-item">
                            <a class="nav-link" href="reports.php">
                                 <i class="fas fa-chart-bar"></i> Reports
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

    <main class="container my-5">
        <div class="card">
            <div class="card-header">
                 <h2><i class="fas fa-briefcase"></i> Job Details</h2>
            </div>
            <div class="card-body">
                <p><strong>Company:</strong> <?php echo htmlspecialchars($job['CompanyName']); ?></p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($job['Location']); ?></p>
                 <p><strong>Category:</strong> <?php echo htmlspecialchars($job['CategoryName']); ?></p>
                <p><strong>Job Requirements:</strong></p>
                <p><?php echo nl2br(htmlspecialchars($job['JobRequirements'])); ?></p>
                <p><strong>Salary Range:</strong> <?php echo htmlspecialchars($job['SalaryRange']); ?></p>
                <p><strong>Job Type:</strong> <?php echo htmlspecialchars($job['JobType']); ?></p>
                <p><strong>Application Deadline:</strong> <?php echo date('M d, Y', strtotime($job['ApplicationDeadline'])); ?></p>
                <p><strong>Status:</strong> <span class="badge <?php echo $job['Status'] === 'Approved' ? 'badge-success' : ($job['Status'] === 'Pending' ? 'badge-warning' : 'badge-danger'); ?>"><?php echo $job['Status']; ?></span></p>
                <p><strong>Posted On:</strong> <?php echo date('M d, Y', strtotime($job['CreatedAt'])); ?></p>
            </div>
            <div class="card-footer">
                <a href="manage-jobs.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Job Listings
                </a>
                <a href="edit-job.php?id=<?php echo $job['Id']; ?>" class="btn btn-warning">
                     <i class="fas fa-edit"></i> Edit Job
                </a>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
