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

// Handle job deletion
if (isset($_GET['delete'])) {
    $jobId = $_GET['delete'];
    $query = "DELETE FROM Job WHERE Id = $jobId AND EmployerId = $employerId";
    mysqli_query($conn, $query);
    $_SESSION['message'] = "Job deleted successfully";
    header("Location: manage-posted-jobs.php");
    exit();
}

// Get jobs with optional status filter
$query = "SELECT j.*, c.CategoryName  
            FROM Job j  
            LEFT JOIN JobCategory c ON j.CategoryId = c.Id  
            WHERE j.EmployerId = $employerId";

if ($statusFilter) {
    $query .= " AND j.Status = '$statusFilter'";
}

$query .= " ORDER BY j.CreatedAt DESC";
$result = mysqli_query($conn, $query);
$jobs = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Manage Job Postings | sheroes</title>
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
                line-height: 1.7;
            }
            nav {
                background-color: HOTPINK;
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
                background-color:HOTPINK;
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
                color: #HOTPINK;
                margin-bottom: 0;
                display: flex;
                align-items: center;
                gap: 10px;
                font-weight: 600;
            }
            .btn-primary {
                background-color:HOTPINK;
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
                color: HOTPINK;
                border-color:HOTPINK;
            }
            .btn-outline-primary:hover {
                background-color: #f0f4f8;
                color:HOTPINK;
            }
            .btn-outline-warning {
                color:HOTPINK;
                border-color:HOTPINK;
            }
            .btn-outline-warning:hover {
                background-color: #fff6e5;
                color:HOTPINK;
            }
            .btn-outline-success {
                color:HOTPINK;
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
            .badge-pending {
                background-color:HOTPINK;
                color:WHITE;
            }
            .badge-approved {
                background-color:HOTPINK;
            }
            .badge-rejected {
                background-color:HOTPINK;
            }
            .filter-buttons .btn {
                margin-right: 5px;
                margin-bottom: 5px;
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
            #jobsTable thead th {
                background-color: #f7fafc;
                color: #4a5568;
                font-weight: 600;
                border-bottom: 2px solid #e0e0e0;
                padding: 1.2rem 1rem;
                vertical-align: middle;
            }
            #jobsTable tbody td {
                padding: 1.2rem 1rem;
                vertical-align: middle;
                border-bottom: 1px solid #e0e0e0;
            }
            #jobsTable tbody tr:hover {
                background-color: #f7fafc;
                transition: background-color 0.3s ease;
            }
            .table-actions {
                display: flex;
                gap: 0.5rem;
                justify-content: center;
            }
            .table-actions .btn {
                padding: 0.5rem;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 2.5rem;
                height: 2.5rem;
                border-radius: 8px;
                transition: all 0.3s ease;
            }
            .table-actions .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 3px 7px rgba(0, 0, 0, 0.1);
            }
            .table-actions .btn-info {
                background-color:HOTPINK;
                border: none;
            }
            .table-actions .btn-info:hover {
                background-color:HOTPINK;
            }
            .table-actions .btn-warning {
                background-color:HOTPINK;
                border: none;
            }
            .table-actions .btn-warning:hover {
                background-color:HOTPINK;
            }
            .table-actions .btn-danger {
                background-color:HOTPINK;
                border: none;
            }.btn-outline-success {
     --bs-btn-color: HOTPINK; 
    bs-btn-border-color:HOTPINK; 
    --bs-btn-hover-color: #fff;
    --bs-btn-hover-bg:HOTPINK;
    --bs-btn-hover-border-color: HOTPINK;
    --bs-btn-focus-shadow-rgb: 25,135,84;
    --bs-btn-active-color: #fff;
    --bs-btn-active-bg: HOTPINK;
    --bs-btn-active-border-color: HOTPINK;
    --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
    --bs-btn-disabled-color: HOTPINK;
    --bs-btn-disabled-bg: transparent;
    --bs-btn-disabled-border-color:HOTPINK;
    --bs-gradient: none;
}
.pagination {
    --bs-pagination-padding-x: 0.75rem;
    --bs-pagination-padding-y: 0.375rem;
    --bs-pagination-font-size: 1rem;
    --bs-pagination-color: HOTPINK;
    --bs-pagination-bg: var(--bs-body-bg);
    --bs-pagination-border-width: var(--bs-border-width);
    --bs-pagination-border-color: var(--bs-border-color);
    --bs-pagination-border-radius: var(--bs-border-radius);
    --bs-pagination-hover-color: HOTPINK;
    --bs-pagination-hover-bg: var(--bs-tertiary-bg);
    --bs-pagination-hover-border-color: var(--bs-border-color);
    --bs-pagination-focus-color: var(--bs-link-hover-color);
    --bs-pagination-focus-bg: var(--bs-secondary-bg);
    --bs-pagination-focus-box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    --bs-pagination-active-color: #fff;
    --bs-pagination-active-bg: #fd0d77;
    --bs-pagination-active-border-color: #0d6efd;
    --bs-pagination-disabled-color: var(--bs-secondary-color);
    --bs-pagination-disabled-bg: var(--bs-secondary-bg);
    --bs-pagination-disabled-border-color: var(--bs-border-color);
    display: flex;
    padding-left: 0;
    list-style: none;
}
            .table-actions .btn-danger:hover {
                background-color:HOTPINK;
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
                    <h1><i class="fas fa-briefcase"></i> Manage Job Postings</h1>
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
                        <a href="manage-posted-jobs.php" class="btn btn-outline-primary btn-lg">All</a>
                        <a href="manage-posted-jobs.php?status=Pending" class="btn btn-outline-warning btn-lg">Pending</a>
                        <a href="manage-posted-jobs.php?status=Approved" class="btn btn-outline-success btn-lg">Approved</a>
                        <a href="manage-posted-jobs.php?status=Rejected" class="btn btn-outline-danger btn-lg">Rejected</a>
                    </div>

                    <div class="table-responsive">
                        <table id="jobsTable" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Type</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Posted</th>
                                    <th>Deadline</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
<?php foreach ($jobs as $job): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($job['JobTitle']); ?></td>
                                        <td><?php echo htmlspecialchars($job['CategoryName']); ?></td>
                                        <td><?php echo $job['JobType']; ?></td>
                                        <td><?php echo htmlspecialchars($job['Location']); ?></td>
                                        <td>
                                            <span class="badge rounded-pill
                                            <?php
                                                  echo $job['Status'] == 'Pending' ? 'badge-pending' :
                                                          ($job['Status'] == 'Approved' ? 'badge-approved' : 'badge-rejected');
                                                  ?>">
    <?php echo $job['Status']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($job['CreatedAt'])); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($job['ApplicationDeadline'])); ?></td>
                                        <td class="table-actions">
                                            <a href="view-job-by-employee.php?id=<?php echo $job['Id']; ?>" class="btn btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="edit-job-by-employee.php?id=<?php echo $job['Id']; ?>" class="btn btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="manage-posted-jobs.php?delete=<?php echo $job['Id']; ?>" class="btn btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this job?');">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
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
                                                        order: [[5, 'desc']], // Sort by posted date descending
                                                        language: {
                                                            paginate: {
                                                                previous: '<i class="fas fa-chevron-left"></i>',
                                                                next: '<i class="fas fa-chevron-right"></i>'
                                                            },
                                                            lengthMenu: 'Show _MENU_ entries',
                                                            search: 'Search:',
                                                            info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                                                            infoEmpty: 'Showing 0 to 0 of 0 entries',
                                                            infoFiltered: '(filtered from _MAX_ total entries)',
                                                            zeroRecords: 'No matching records found'
                                                        }
                                                    });
                                                });
        </script>
    </body>
</html>
