<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'jobseeker') {
    header("Location: login.php");
    exit();
}
include 'config.php';

// Get search and filter parameters
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$category = isset($_GET['category']) ? intval($_GET['category']) : 0;
$location = isset($_GET['location']) ? mysqli_real_escape_string($conn, $_GET['location']) : '';
$jobType = isset($_GET['job_type']) ? $_GET['job_type'] : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 6;

// Base query
$query = "SELECT j.*, e.CompanyName, c.CategoryName, e.Logo as CompanyLogo
            FROM Job j
            JOIN Employer e ON j.EmployerId = e.Id
            LEFT JOIN JobCategory c ON j.CategoryId = c.Id
            WHERE j.Status = 'Approved'";

// Add filters
if (!empty($search)) {
    $query .= " AND (j.JobTitle LIKE '%$search%' OR j.JobRequirements LIKE '%$search%' OR e.CompanyName LIKE '%$search%')";
}
if ($category > 0) {
    $query .= " AND j.CategoryId = $category";
}
if (!empty($location)) {
    $query .= " AND j.Location LIKE '%$location%'";
}
if (!empty($jobType)) {
    $query .= " AND j.JobType = '$jobType'";
}

// Get total count for pagination
$countQuery = str_replace('j.*, e.CompanyName, c.CategoryName, e.Logo as CompanyLogo', 'COUNT(*) as total', $query);
$countResult = mysqli_query($conn, $countQuery);
$totalJobs = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalJobs / $perPage);

// Add sorting and pagination
$query .= " ORDER BY j.CreatedAt DESC LIMIT " . (($page - 1) * $perPage) . ", $perPage";
$result = mysqli_query($conn, $query);

// Get categories for filter dropdown
$categories = mysqli_query($conn, "SELECT * FROM JobCategory");
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Browse Jobs | sheroes</title>
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
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                padding: 1rem 0;
            }

            .navbar-brand {
                color:hotpink;
                font-weight: 600;
                font-size: 1.8rem;
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



            .container {
                padding-top: 2rem;
                padding-bottom: 2rem;
            }
            .page-header {
                
                padding: 2rem;
                border-radius: 12px;
                margin-bottom: 2rem;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            }
            .page-header h2 {
                color:hotpink; /* Teal/Cyan */
                font-weight: 600;
                margin-bottom: 0.5rem;
                font-size: 2.2rem;
            }
            .job-card {
                background-color: #ffffff;
                border-radius: 12px;
                box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08); /* More pronounced shadow */
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                margin-bottom: 2rem;
                height: 100%;
                display: flex;
                flex-direction: column;
            }
            .job-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); /* Increased shadow on hover */
            }
            .job-card-body {
                padding: 2rem;
                flex-grow: 1;
                display: flex;
                flex-direction: column;
            }
            .job-title {
                color:hotpink; /* Darker title */
                font-size: 1.4rem;
                margin-bottom: 0.5rem;
                font-weight: 600;
            }
            .company-name {
                color:hotpink; /* Muted company name */
                font-size: 1.1rem;
                margin-bottom: 1rem;
                display: flex;
                align-items: center; /* Vertically align logo and name */
                gap: 0.5rem; /* Space between logo and name */
            }
            .company-logo {
                width: 30px; /* Smaller logo */
                height: 30px;
                border-radius: 6px; /* Rounded corners for logo */
                object-fit: cover; /* Ensure logo doesn't distort */
            }
            .job-location {
                color: #555;
                margin-bottom: 0.75rem;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }
            .job-salary {
                color: #555;
                margin-bottom: 1.2rem;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }
            .job-type {
                font-size: 0.9rem;
                padding: 0.5rem 1rem;
                border-radius: 12px;
                margin-right: 0.5rem;
                background-color: #e0f7fa; /* Light background for badge */
                color:hotpink; /* Teal/Cyan for badge text */
                font-weight: 500;
            }
            .job-category {
                font-size: 0.9rem;
                padding: 0.5rem 1rem;
                border-radius: 12px;
                margin-right: 0.5rem;
                background-color: #f0f4f8; /* Light background for badge */
                color:hotpink;
                font-weight: 500;
            }
            .job-description {
                color: #666;
                line-height: 1.7;
                margin-bottom: 1.5rem;
                display: -webkit-box;
                -webkit-line-clamp: 3;
                -webkit-box-orient: vertical;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .job-meta {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-top: auto; /* Push meta to bottom */
            }
            .job-posted {
                color: #999;
                font-size: 0.9rem;
            }
            .job-actions {
                display: flex;
                gap: 0.75rem;
            }
            .btn-success {
    --bs-btn-color: #fff;
    --bs-btn-bg: hotpink;
    --bs-btn-border-color: hotpink;
    --bs-btn-hover-color: #fff;
    --bs-btn-hover-bg: #157347;
    --bs-btn-hover-border-color: #146c43;
    --bs-btn-focus-shadow-rgb: 60,153,110;
    --bs-btn-active-color: #fff;
    --bs-btn-active-bg: #146c43;
    --bs-btn-active-border-color: #13653f;
    --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
    --bs-btn-disabled-color: #fff;
    --bs-btn-disabled-bg: #198754;
    --bs-btn-disabled-border-color: #198754;
}
            .btn-primary {
                background-color: hotpink;
                border-color:hotpink;
                color: white;
                padding: 0.75rem 1.5rem;
                font-weight: 500;
                transition: background-color 0.3s ease, border-color 0.3s ease, transform 0.2s ease;
            }
            .btn-primary:hover {
                background-color: hotpink;
                border-color: hotpink;
                transform: translateY(-2px);
            }
            .btn-outline-primary {
                color:hotpink;
                border-color:hotpink;
                padding: 0.75rem 1.5rem;
                font-weight: 500;
                transition: color 0.3s ease, border-color 0.3s ease, transform 0.2s ease;
            }
            .btn-outline-primary:hover {
                background-color: #f0f8fa;
                color:hotpink;
                border-color:hotpink;
                transform: translateY(-2px);
            }
            .save-btn {
                background-color: transparent;
                border: none;
                color: #555;
                padding: 0.5rem;
                font-size: 1.2rem;
                cursor: pointer;
                transition: color 0.2s ease;
                min-width: unset;
            }
            .save-btn:hover {
                color:hotpink;
            }
            .save-btn.saved {
                color:hotpink;
            }
            .filter-section {
                background-color: #ffffff;
                border-radius: 12px;
                padding: 2rem;
                margin-bottom: 2rem;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            }
            .filter-title {
                margin-bottom: 1.5rem;
                color:hotpink;
                font-size: 1.4rem;
                font-weight: 600;
            }
            .form-control {
                border-radius: 6px;
                border-color: #e0e0e0;
                padding: 0.75rem;
                font-size: 1rem;
            }
            .form-select {
                border-radius: 6px;
                border-color: #e0e0e0;
                padding: 0.75rem;
                font-size: 1rem;
            }
            .btn-primary {
                border-radius: 6px;
            }
            .pagination .page-item.active .page-link {
                background-color:hotpink;
                border-color:hotpink;
                color: white;
            }
            .pagination .page-link {
                border-radius: 6px;
                color: #555;
                transition: color 0.2s ease, background-color 0.2s ease;
            }
            .pagination .page-link:hover {
                background-color: #f0f8fa;
                color:hotpink;
            }
            .pagination .page-item:first-child .page-link {
                border-top-left-radius: 6px;
                border-bottom-left-radius: 6px;
            }
            .pagination .page-item:last-child .page-link {
                border-top-right-radius: 6px;
                border-bottom-right-radius: 6px;
            }
            .no-jobs-message {
                text-align: center;
                padding: 2rem;
                background-color: #ffffff;
                border-radius: 12px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
                margin-top: 2rem;
            }
            .no-jobs-message i {
                font-size: 3rem;
                color: #999;
                margin-bottom: 1rem;
            }
            .no-jobs-message h4 {
                font-size: 1.5rem;
                color: #555;
                margin-bottom: 0.5rem;
            }
            .no-jobs-message p {
                color: #777;
            }
        </style>
    </head>
    <body>
        <?php include 'includes/jobseeker-navbar.php'; ?>
        <br>
        <br>
        <br>

        <div class="container">
            <div class="page-header">
                <h2><i class="fas fa-search"></i> Browse Jobs</h2>
            </div>

            

           

            <div class="row">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($job = mysqli_fetch_assoc($result)): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card job-card">
                                <div class="job-card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h2 class="job-title"><?= htmlspecialchars($job['JobTitle']) ?></h2>
                                            <div class="company-name">
                                                <?php if ($job['CompanyLogo']): ?>
                                                    <img src="<?= htmlspecialchars($job['CompanyLogo']) ?>" alt="<?= htmlspecialchars($job['CompanyName']) ?>" class="company-logo">
                                                <?php endif; ?>
                                                <?= htmlspecialchars($job['CompanyName']) ?>
                                            </div>
                                        </div>
                                        <?php
?>
                                        
                                    </div>

                                    <div class="d-flex flex-wrap mb-3">
                                        <span class="job-type"><?= $job['JobType'] ?></span>
                                        <?php if ($job['CategoryName']): ?>
                                            <span class="job-category"><?= $job['CategoryName'] ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="job-location">
                                        <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($job['Location']) ?>
                                    </div>
                                    <?php if ($job['SalaryRange']): ?>
                                        <div class="job-salary">
                                            <i class="fas fa-money-bill-wave"></i> <?= htmlspecialchars($job['SalaryRange']) ?>
                                        </div>
                                    <?php endif; ?>

                                    <p class="job-description">
                                        <?= nl2br(htmlspecialchars(substr($job['JobRequirements'], 0, 200))) ?>...
                                    </p>

                                    <div class="job-meta">
                                        <small class="job-posted">Posted: <?= date('M d, Y', strtotime($job['CreatedAt'])) ?></small>
                                        <div class="job-actions">
                                            <a href="job-details.php?id=<?= $job['Id'] ?>" class="btn btn-sm btn-outline-primary">Details</a>
                                            <a href="apply-job.php?job_id=<?= $job['Id'] ?>" class="btn btn-sm btn-success">Apply</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="no-jobs-message">
                            <i class="fas fa-search"></i>
                            <h4>No jobs found matching your criteria</h4>
                            <p>Try adjusting your search filters</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <nav aria-label="Job pagination">
                    <ul class="pagination justify-content-center mt-4">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Enhanced save job functionality
            document.querySelectorAll('.save-btn').forEach(button => {
                button.addEventListener('click', async function () {
                    const jobId = this.getAttribute('data-job-id');
                    const icon = this.querySelector('i');
                    const isSaved = this.classList.contains('saved');

                    // Optimistic UI update
                    this.disabled = true;
                    if (isSaved) {
                        this.classList.remove('saved');
                        this.classList.add('btn-outline-secondary');
                        icon.classList.replace('fas', 'far');
                        this.innerHTML = '<i class="far fa-bookmark"></i> Save';
                    } else {
                        this.classList.remove('btn-outline-secondary');
                        this.classList.add('saved');
                        icon.classList.replace('far', 'fas');
                        this.innerHTML = '<i class="fas fa-bookmark"></i> Saved';
                    }

                    try {
                        const response = await fetch('save-jobs.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `job_id=${jobId}&action=${isSaved ? 'unsave' : 'save'}`
                        });

                        const data = await response.json();

                        if (!data.success) {
                            // Revert UI if API call failed
                            if (isSaved) {
                                this.classList.add('saved');
                                this.classList.remove('btn-outline-secondary');
                                icon.classList.replace('far', 'fas');
                                this.innerHTML = '<i class="fas fa-bookmark"></i> Saved';
                            } else {
                                this.classList.remove('saved');
                                this.classList.add('btn-outline-secondary');
                                icon.classList.replace('fas', 'far');
                                this.innerHTML = '<i class="far fa-bookmark"></i> Save';
                            }

                            alert(data.message || 'Operation failed. Please try again.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        // Revert UI on network error
                        if (isSaved) {
                            this.classList.add('saved');
                            this.classList.remove('btn-outline-secondary');
                            icon.classList.replace('far', 'fas');
                            this.innerHTML = '<i class="fas fa-bookmark"></i> Saved';
                        } else {
                            this.classList.remove('saved');
                            this.classList.add('btn-outline-secondary');
                            icon.classList.replace('fas', 'far');
                            this.innerHTML = '<i class="far fa-bookmark"></i> Save';
                        }

                        alert('Network error. Please check your connection and try again.');
                    } finally {
                        this.disabled = false;
                    }
                });
            });
        </script>
    </body>
</html>
