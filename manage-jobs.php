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

// Handle job deletion safely
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $query = "DELETE FROM Job WHERE Id = $id";
    mysqli_query($conn, $query);
    $_SESSION['message'] = "Job deleted successfully";
    header("Location: manage-jobs.php");
    exit();
}

// Fetch all job postings
$jobs = [];
$query = "SELECT j.Id, j.JobTitle, j.CompanyName, j.Location, j.SalaryRange, j.JobType, j.ApplicationDeadline, j.Status, j.CreatedAt, jc.CategoryName
            FROM Job j
            LEFT JOIN JobCategory jc ON j.CategoryId = jc.Id
            ORDER BY j.CreatedAt DESC";
$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $jobs[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Jobs | sheroes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
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
            background-color: HOTPINK;
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
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #fff;
            padding: 1.5rem;
            border-bottom: 1px solid #e0e0e0;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }
         .card-header h1{
            font-size: 2rem;
            color:HOTPINK;
            font-weight: 600;
            margin-bottom: 0;
            display: flex;
            align-items: center;
            gap: 15px;
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
        .card-body {
            padding: 2rem;
        }
        .table-responsive {
            padding: 0;
            border: none;
        }
        table.dataTable.no-footer {
            border-bottom: none;
        }
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 1.5rem;
        }
        .dataTables_wrapper .dataTables_length {
            margin-bottom: 1.5rem;
        }
        .dataTables_wrapper .dataTables_info {
            margin-top: 1.5rem;
        }
        .table-striped > tbody > tr:nth-child(odd) > td,
        .table-striped > tbody > tr:nth-child(odd) > th {
            background-color: #f8f8f8;
        }
        .table-hover > tbody > tr:hover > td,
        .table-hover > tbody > tr:hover > th {
            background-color: rgba(0, 123, 255, 0.05);
        }
        .badge {
            border-radius: 12px;
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
        }
        .badge-success {
            background-color:HOTPINK;
            color: #fff;
        }
        .badge-warning {
            background-color:HOTPINK;
            color: #fff;
        }
        .badge-danger {
            background-color: HOTPINK;
            color: #fff;
        }
        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background-color:HOTPINK;
            border-color: HOTPINK;
            color: #fff;
        }
        .btn-primary:hover {
            background-color:HOTPINK;
            border-color: HOTPINK;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .btn-warning {
            background-color: HOTPINK;
            border-color: HOTPINK;
            color: #fff;
        }
        .btn-warning:hover {
            background-color: HOTPINK;
            border-color: HOTPINK;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .btn-danger {
            background-color: HOTPINK;
            border-color: HOTPINK;
            color: #fff;
        }
        .btn-danger:hover {
            background-color:HOTPINK;
            border-color: HOTPINK;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .text-muted {
            font-size: 0.9rem;
            color: #999;
        }
        .mt-4 {
            margin-top: 2.5rem !important;
        }
        .card .card-header{
                display: flex;
                justify-content: space-between;
                align-items: center;
            }.pagination {
    --bs-pagination-padding-x: 0.75rem;
    --bs-pagination-padding-y: 0.375rem;
    --bs-pagination-font-size: 1rem;
    --bs-pagination-color: WHITE;
    --bs-pagination-bg: HOTPINK;
    --bs-pagination-border-width: var(--bs-border-width);
    --bs-pagination-border-color: var(--bs-border-color);
    --bs-pagination-border-radius: var(--bs-border-radius);
    --bs-pagination-hover-color: HOTPINK;
    --bs-pagination-hover-bg: var(--bs-tertiary-bg);
    --bs-pagination-hover-border-color: var(--bs-border-color);
    --bs-pagination-focus-color: var(--bs-link-hover-color);
    --bs-pagination-focus-bg: var(--bs-secondary-bg);
    --bs-pagination-focus-box-shadow: 0 0 0 0.25rem HOTPINK;
    --bs-pagination-active-color: #fff;
    --bs-pagination-active-bg:HOTPINK;
    --bs-pagination-active-border-color: HOTPINK;
    --bs-pagination-disabled-color: var(--bs-secondary-color);
    --bs-pagination-disabled-bg: var(--bs-secondary-bg);
    --bs-pagination-disabled-border-color: var(--bs-border-color);
    display: flex;
    padding-left: 0;
    list-style: none;
}.active>.page-link, .page-link.active {
    z-index: 3;
    color: var(--bs-pagination-active-color);
    
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
                        <a class="nav-link text-danger" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container">
        <div class="card">
            <div class="card-header">
                <h1><i class="fas fa-briefcase"></i> Manage Jobs</h1>
                <div>
                  
                </div>
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
                <div class="table-responsive">
                        <table id="jobsTable" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Job Title</th>
                                    <th>Company Name</th>
                                    <th>Location</th>
                                    <th>Category</th>
                                    <th>Salary</th>
                                    <th>Job Type</th>
                                    <th>Deadline</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($jobs as $job): ?>
                                    <tr>
                                        <td><?php echo $job['Id']; ?></td>
                                        <td><?php echo htmlspecialchars($job['JobTitle']); ?></td>
                                        <td><?php echo htmlspecialchars($job['CompanyName']); ?></td>
                                        <td><?php echo htmlspecialchars($job['Location']); ?></td>
                                         <td><?php echo htmlspecialchars($job['CategoryName']); ?></td>
                                        <td><?php echo htmlspecialchars($job['SalaryRange']); ?></td>
                                        <td><?php echo htmlspecialchars($job['JobType']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($job['ApplicationDeadline'])); ?></td>
                                        <td>
                                            <span class="badge <?php echo $job['Status'] == 'Approved' ? 'badge-success' : ($job['Status'] == 'Rejected' ? 'badge-danger' : 'badge-warning'); ?>">
                                                <?php echo ucfirst($job['Status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($job['CreatedAt'])); ?></td>
                                        <td>
                                            <div style="display: flex; gap: 0.5rem;">
                                                <a href="view-job.php?id=<?php echo $job['Id']; ?>" class="btn btn-sm btn-primary" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="edit-job.php?id=<?php echo $job['Id']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="manage-jobs.php?delete=<?php echo $job['Id']; ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this job?');">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#jobsTable').DataTable({
                responsive: true,
                order: [[9, 'desc']] // Sort by created date (column 9) descending
            });
        });
    </script>
</body>
</html>
