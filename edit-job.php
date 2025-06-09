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
    $query = "SELECT j.Id, j.JobTitle, j.CompanyName, j.Location, j.JobRequirements, j.SalaryRange, j.JobType, j.ApplicationDeadline, j.Status, j.CategoryId, c.CategoryName  
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

    // Handle form submission to update job
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $jobTitle = mysqli_real_escape_string($conn, $_POST['JobTitle']);
        $companyName = mysqli_real_escape_string($conn, $_POST['CompanyName']);
        $location = mysqli_real_escape_string($conn, $_POST['Location']);
        $jobRequirements = mysqli_real_escape_string($conn, $_POST['JobRequirements']);
        $salaryRange = mysqli_real_escape_string($conn, $_POST['SalaryRange']);
        $jobType = $_POST['JobType'];
        $applicationDeadline = $_POST['ApplicationDeadline'];
        $categoryId = $_POST['CategoryId'];
        $status = $_POST['Status'];

        // Update job details
        $updateQuery = "UPDATE Job  
                            SET JobTitle = '$jobTitle', CompanyName = '$companyName', Location = '$location', JobRequirements = '$jobRequirements',  
                                 SalaryRange = '$salaryRange', JobType = '$jobType', ApplicationDeadline = '$applicationDeadline',  
                                 CategoryId = '$categoryId', Status = '$status'  
                            WHERE Id = $jobId";

        if (mysqli_query($conn, $updateQuery)) {
            $_SESSION['message'] = "Job updated successfully";
            header("Location: manage-jobs.php");
            exit();
        } else {
            $_SESSION['error'] = "Error updating job.";
        }
    }
}

// Get job categories for the dropdown
$categoriesQuery = "SELECT Id, CategoryName FROM JobCategory";
$categoriesResult = mysqli_query($conn, $categoriesQuery);
$categories = [];
while ($row = mysqli_fetch_assoc($categoriesResult)) {
    $categories[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Job | sheroes</title>
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
            background-color:hotpink;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h2 {
    color: hotpink;
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
        .form-label {
            font-weight: 500;
            color:hotpink;
            margin-bottom: 0.75rem;
            display: block;
            font-size: 1.1rem;
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            padding: 0.75rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color:hotpink;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            outline: none;
        }
        .form-control {
            height: auto;
        }
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        .btn-success {
            background-color: hotpink;
            border-color: hotpink;
            color: #fff;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 1rem;
        }
        .btn-success:hover {
            background-color: hotpink;
            border-color: hotpink;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .btn-secondary {
            background-color:hotpink;
            border-color: hotpink;
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
        .card-footer {
            background-color: #fff;
            padding: 1.5rem;
            border-top: hotpink;
            border-bottom-left-radius: 12px;
            border-bottom-right-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .alert-danger {
            background-color: hotpink;
            border-color:hotpink;
            color:hotpink;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            border-radius: 8px;
            font-size: 1rem;
        }
        .alert-danger .btn-close {
            color:hotpink;
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
            background-color: hotpink;
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
        <h2><i class="fas fa-edit"></i> Edit Job: <?php echo htmlspecialchars($job['JobTitle']); ?></h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error'];
                unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-edit"></i> Edit Job Details</h2>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="JobTitle" class="form-label">Job Title</label>
                        <input type="text" class="form-control" id="JobTitle" name="JobTitle" value="<?php echo htmlspecialchars($job['JobTitle']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="CompanyName" class="form-label">Company Name</label>
                        <input type="text" class="form-control" id="CompanyName" name="CompanyName" value="<?php echo htmlspecialchars($job['CompanyName']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="Location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="Location" name="Location" value="<?php echo htmlspecialchars($job['Location']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="JobRequirements" class="form-label">Job Requirements</label>
                        <textarea class="form-control" id="JobRequirements" name="JobRequirements" rows="4" required><?php echo htmlspecialchars($job['JobRequirements']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="SalaryRange" class="form-label">Salary Range</label>
                        <input type="text" class="form-control" id="SalaryRange" name="SalaryRange" value="<?php echo htmlspecialchars($job['SalaryRange']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="JobType" class="form-label">Job Type</label>
                        <select class="form-select" id="JobType" name="JobType" required>
                            <option value="Full-time" <?php echo $job['JobType'] === 'Full-time' ? 'selected' : ''; ?>>Full-time</option>
                            <option value="Part-time" <?php echo $job['JobType'] === 'Part-time' ? 'selected' : ''; ?>>Part-time</option>
                            <option value="Contract" <?php echo $job['JobType'] === 'Contract' ? 'selected' : ''; ?>>Contract</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="ApplicationDeadline" class="form-label">Application Deadline</label>
                        <input type="date" class="form-control" id="ApplicationDeadline" name="ApplicationDeadline" value="<?php echo $job['ApplicationDeadline']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="CategoryId" class="form-label">Job Category</label>
                        <select class="form-select" id="CategoryId" name="CategoryId" required>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['Id']; ?>" <?php echo $category['Id'] == $job['CategoryId'] ? 'selected' : ''; ?>><?php echo $category['CategoryName']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="Status" class="form-label">Status</label>
                        <select class="form-select" id="Status" name="Status" required>
                            <option value="Pending" <?php echo $job['Status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="Approved" <?php echo $job['Status'] === 'Approved' ? 'selected' : ''; ?>>Approved</option>
                            <option value="Rejected" <?php echo $job['Status'] === 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                        </select>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="manage-jobs.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Job Listings
                    </a>
                </div>
            </div>
        </form>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
