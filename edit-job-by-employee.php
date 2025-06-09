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
$jobId = $_GET['id'] ?? 0;
$errors = [];
$success = '';

// Get job details
$query = "SELECT * FROM Job WHERE Id = ? AND EmployerId = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'ii', $jobId, $employerId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$job = mysqli_fetch_assoc($result);

if (!$job) {
    header("Location: not-found.php");
    exit();
}

// Get job categories
$categories = [];
$query = "SELECT * FROM JobCategory";
$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jobTitle = trim($_POST['job_title']);
    $companyName = trim($_POST['company_name']);
    $location = trim($_POST['location']);
    $requirements = trim($_POST['requirements']);
    $salaryRange = trim($_POST['salary_range']);
    $jobType = $_POST['job_type'];
    $deadline = $_POST['deadline'];
    $categoryId = $_POST['category_id'];

    // Validate inputs
    if (empty($jobTitle)) $errors[] = "Job title is required";
    if (empty($companyName)) $errors[] = "Company name is required";
    if (empty($location)) $errors[] = "Location is required";
    if (empty($requirements)) $errors[] = "Job requirements are required";
    if (empty($deadline)) $errors[] = "Application deadline is required";
    if (strtotime($deadline) < strtotime('today')) $errors[] = "Deadline must be in the future";

    if (empty($errors)) {
        $query = "UPDATE Job SET
            JobTitle = ?,
            CompanyName = ?,
            Location = ?,
            JobRequirements = ?,
            SalaryRange = ?,
            JobType = ?,
            ApplicationDeadline = ?,
            CategoryId = ?
            WHERE Id = ? AND EmployerId = ?";
        
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'sssssssiii', 
            $jobTitle, $companyName, $location, $requirements,
            $salaryRange, $jobType, $deadline, $categoryId,
            $jobId, $employerId
        );
        
        if (mysqli_stmt_execute($stmt)) {
            $success = "Job updated successfully!";
            // Refresh job data
            $query = "SELECT * FROM Job WHERE Id = ? AND EmployerId = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'ii', $jobId, $employerId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $job = mysqli_fetch_assoc($result);
        } else {
            $errors[] = "Error updating job: " . mysqli_error($conn);
        }
    }
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
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.6;
            padding-top: 20px;
            padding-bottom: 40px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .sidebar {
            background-color:HOTPINK;
            min-height: 100vh;
            color: white;
        }
        h1 {
    COLOR: HOTPINK;
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
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px HOTPINK;
            margin-bottom: 20px;
        }
        .form-label {
            font-weight: 600;
        }
        .form-control, .form-select {
            border-radius: 8px;
            padding: 10px 15px;
        }
        .btn-primary {
            background-color:HOTPINK;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: HOTPINK;
        }
        .error {
            color:HOTPINK;
            font-size: 0.875em;
        }
        .job-status-badge {
            font-size: 1rem;
            padding: 8px 15px;
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
                    <h1><i class="fas fa-edit"></i> Edit Job</h1>
                    <div>
                       
                        <a href="manage-posted-jobs.php" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-arrow-left"></i> Back to Jobs
                        </a>
                    </div>
                </div>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> Please fix the following issues:
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Edit Job Details</h5>
                            <span class="badge rounded-pill 
                                <?php echo $job['Status'] == 'Pending' ? 'bg-warning' : 
                                      ($job['Status'] == 'Approved' ? 'bg-success' : 'bg-danger'); ?> job-status-badge">
                                <?php echo $job['Status']; ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="edit-job-by-employee.php?id=<?php echo $jobId; ?>" method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="job_title" class="form-label">Job Title *</label>
                                    <input type="text" class="form-control" id="job_title" name="job_title" 
                                           value="<?php echo htmlspecialchars($job['JobTitle']); ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="company_name" class="form-label">Company Name *</label>
                                    <input type="text" class="form-control" id="company_name" name="company_name" 
                                           value="<?php echo htmlspecialchars($job['CompanyName']); ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="location" class="form-label">Location *</label>
                                    <input type="text" class="form-control" id="location" name="location" 
                                           value="<?php echo htmlspecialchars($job['Location']); ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="salary_range" class="form-label">Salary Range</label>
                                    <input type="text" class="form-control" id="salary_range" name="salary_range" 
                                           value="<?php echo htmlspecialchars($job['SalaryRange']); ?>" 
                                           placeholder="e.g. $50,000 - $70,000 per year">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="job_type" class="form-label">Job Type *</label>
                                    <select class="form-select" id="job_type" name="job_type" required>
                                        <option value="Full-time" <?php echo $job['JobType'] == 'Full-time' ? 'selected' : ''; ?>>Full-time</option>
                                        <option value="Part-time" <?php echo $job['JobType'] == 'Part-time' ? 'selected' : ''; ?>>Part-time</option>
                                        <option value="Contract" <?php echo $job['JobType'] == 'Contract' ? 'selected' : ''; ?>>Contract</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="category_id" class="form-label">Job Category *</label>
                                    <select class="form-select" id="category_id" name="category_id" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['Id']; ?>" 
                                                <?php echo $job['CategoryId'] == $category['Id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($category['CategoryName']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="deadline" class="form-label">Application Deadline *</label>
                                    <input type="date" class="form-control" id="deadline" name="deadline" 
                                           value="<?php echo htmlspecialchars($job['ApplicationDeadline']); ?>" required
                                           min="<?php echo date('Y-m-d'); ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="requirements" class="form-label">Job Requirements *</label>
                                <textarea class="form-control" id="requirements" name="requirements" rows="8" required><?php echo htmlspecialchars($job['JobRequirements']); ?></textarea>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save"></i> Update Job
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set minimum date for deadline (today)
        document.getElementById('deadline').min = new Date().toISOString().split('T')[0];
    </script>
</body>
</html>