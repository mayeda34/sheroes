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

// Check if the SavedJobs table exists
$tableCheck = mysqli_query($conn, "SHOW TABLES LIKE 'SavedJobs'");
$tableExists = mysqli_num_rows($tableCheck) > 0;

if ($tableExists) {
    $query = "SELECT j.*, e.CompanyName, c.CategoryName, s.SavedAt
                FROM SavedJobs s
                JOIN Job j ON s.JobId = j.Id
                JOIN Employer e ON j.EmployerId = e.Id
                LEFT JOIN JobCategory c ON j.CategoryId = c.Id
                WHERE s.JobSeekerId = $jobSeekerId
                ORDER BY s.SavedAt DESC";
    $savedJobs = mysqli_query($conn, $query);
} else {
    $savedJobs = false;
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Saved Jobs | sheroes</title>
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
            .saved-jobs-header {
                text-align: center;
                margin-bottom: 40px;
            }
            .saved-jobs-header h2 {
                font-size: 2.2rem;
                font-weight: 700;
                color: HOTPINK;
                margin-bottom: 15px;
            }
            .saved-jobs-header p {
                font-size: 1.1rem;
                color: hotpink;
            }
            .job-card {
                background-color: #fff;
                border-radius: 12px;
                box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
                transition: all 0.3s ease;
                margin-bottom: 25px;
                border: none;
            }
            .job-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
            }
            .job-card-body {
                padding: 25px;
            }
            .job-title {
                font-size: 1.4rem;
                color: HOTPINK;
                font-weight: 600;
                margin-bottom: 12px;
            }
            .company-name {
                font-size: 1.1rem;
                color: #7f8c8d;
                margin-bottom: 16px;
                display: block;
            }
            .job-location {
                font-size: 0.95rem;
                color: #555;
                margin-bottom: 10px;
                display: flex;
                align-items: center;
                gap: 6px;
            }
            .job-salary {
                font-size: 0.95rem;
                color: #555;
                margin-bottom: 10px;
                display: flex;
                align-items: center;
                gap: 6px;
            }
            .job-type {
                display: inline-block;
                padding: 8px 16px;
                border-radius: 16px;
                background-color: #e0f7fa;
                color: HOTPINK;
                font-size: 0.9rem;
                font-weight: 500;
                margin-right: 10px;
                transition: background-color 0.3s ease, color 0.3s ease;
            }
            .job-type:hover {
                background-color:hotpink;
                color: #fff;
            }.navbar-brand {
    color: hotpink;
    font-weight: 600;
    font-size: 1.8rem;
}
            .job-category {
                display: inline-block;
                padding: 8px 16px;
                border-radius: 16px;
                background-color: #f0f0f0;
                color: hotpink;
                font-size: 0.9rem;
                font-weight: 500;
                margin-right: 10px;
                transition: background-color 0.3s ease, color 0.3s ease;
            }
            .job-category:hover {
                background-color:hotpink;
                color: #fff;
            }
            .job-description {
                font-size: 1rem;
                color: #666;
                margin-bottom: 20px;
                line-height: 1.7;
            }
            .job-actions {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-top: 25px;
            }
            .view-details-btn {
                background-color:hotpink;
                color: #fff;
                border-radius: 8px;
                padding: 12px 24px;
                font-size: 1rem;
                font-weight: 500;
                transition: background-color 0.3s ease, transform 0.2s ease;
                border: none;
                box-shadow: 0 3px 7px rgba(0, 0, 0, 0.1);
            }
            .view-details-btn:hover {
                background-color: hotpink;
                transform: translateY(-2px);
                box-shadow: 0 4px 9px rgba(0, 0, 0, 0.12);
            }
            .apply-now-btn {
                background-color:hotpink;
                color: #fff;
                border-radius: 8px;
                padding: 12px 24px;
                font-size: 1rem;
                font-weight: 500;
                transition: background-color 0.3s ease, transform 0.2s ease;
                border: none;
                box-shadow: 0 3px 7px rgba(0, 0, 0, 0.1);
            }
            .apply-now-btn:hover {
                background-color:hotpink;
                transform: translateY(-2px);
                box-shadow: 0 4px 9px rgba(0, 0, 0, 0.12);
            }
            .unsave-job-btn {
                background-color: hotpink;
                color: #fff;
                border-radius: 8px;
                padding: 10px 18px;
                font-size: 0.9rem;
                font-weight: 500;
                transition: background-color 0.3s ease, transform 0.2s ease;
                border: none;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
                display: flex;
                align-items: center;
                gap: 6px;
            }
            .unsave-job-btn:hover {
                background-color:hotpink;
                transform: translateY(-2px);
                box-shadow: 0 3px 7px rgba(0, 0, 0, 0.1);
            }
            .empty-state {
                text-align: center;
                padding: 60px 0;
                border-radius: 12px;
                background-color: #f7fafc;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
                margin-top: 30px;
            }
            .empty-state i {
                font-size: 4rem;
                color:hotpink;
                margin-bottom: 25px;
            }
            .empty-state h3 {
                font-size: 1.8rem;
                color:hotpink;
                margin-bottom: 15px;
                font-weight: 600;
            }
            .empty-state p {
                font-size: 1.1rem;
                color:hotpink;
                margin-bottom: 30px;
            }
            .browse-jobs-btn {
                background-color:hotpink;
                color: #fff;
                border-radius: 8px;
                padding: 14px 32px;
                font-size: 1.1rem;
                font-weight: 600;
                transition: background-color 0.3s ease, transform 0.2s ease;
                border: none;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            }
            .browse-jobs-btn:hover {
                background-color:hotpink;
                transform: translateY(-2px);
                box-shadow: 0 5px 12px rgba(0, 0, 0, 0.2);
            }
            .saved-at {
                font-size: 0.9rem;
                color:hotpink;
                margin-top: 10px;
                display: block;
            }

            .navbar {
                background-color: #ffffff;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                padding: 1rem 0;
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
        </style>
    </head>
    <body>
        <?php include 'includes/jobseeker-navbar.php'; ?>

        <div class="container py-5">
            <div class="row">
                <div class="col-lg-12">
                    <div class="saved-jobs-header">
                        <h2><i class="fas fa-bookmark"></i> My Saved Jobs</h2>
                        <p>Here are the jobs you've saved.  You can view the details or apply.</p>
                    </div>

                    <?php if (!$tableExists): ?>
                        <div class="alert alert-warning">
                            The saved jobs feature is not available at the moment. Please check back later.
                        </div>
                    <?php elseif (!$savedJobs || mysqli_num_rows($savedJobs) == 0): ?>
                        <div class="empty-state">
                            <i class="far fa-bookmark"></i>
                            <h3>No Saved Jobs</h3>
                            <p>You haven't saved any jobs yet. Browse jobs and click the bookmark icon to save them.</p>
                            <a href="browse-jobs.php" class="btn browse-jobs-btn">Browse Jobs</a>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php while ($job = mysqli_fetch_assoc($savedJobs)): ?>
                                <div class="col-md-6">
                                    <div class="card job-card">
                                        <div class="card-body job-card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h5 class="job-title"><?= htmlspecialchars($job['JobTitle']) ?></h5>
                                                    <span class="company-name"><?= htmlspecialchars($job['CompanyName']) ?></span>
                                                </div>
                                                <button class="btn unsave-job-btn" data-job-id="<?= $job['Id'] ?>">
                                                    <i class="fas fa-trash-alt"></i> Remove
                                                </button>
                                            </div>
                                            <div class="job-meta">
                                                <span class="job-location"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($job['Location']) ?></span>
                                                <?php if ($job['SalaryRange']): ?>
                                                    <span class="job-salary">| <i class="fas fa-money-bill-wave"></i> <?= htmlspecialchars($job['SalaryRange']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="job-categories">
                                                <span class="job-type"><?= $job['JobType'] ?></span>
                                                <?php if ($job['CategoryName']): ?>
                                                    <span class="job-category"><?= $job['CategoryName'] ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <p class="job-description"><?= substr(htmlspecialchars($job['JobRequirements']), 0, 180) ?>...</p>
                                            <span class="saved-at">Saved: <?= date('M d, Y', strtotime($job['SavedAt'])) ?></span>
                                            <div class="job-actions">
                                                <a href="job-details.php?id=<?= $job['Id'] ?>" class="btn view-details-btn">View Details</a>
                                                <a href="apply-job.php?job_id=<?= $job['Id'] ?>" class="btn apply-now-btn">Apply Now</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Unsave job functionality
            document.querySelectorAll('.unsave-job-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const jobId = this.getAttribute('data-job-id');
                    const card = this.closest('.job-card');

                    fetch('save-job.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `job_id=${jobId}&action=unsave`
                    })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    card.style.opacity = '0';
                                    setTimeout(() => {
                                        card.remove();
                                        // Check if no jobs left
                                        if (document.querySelectorAll('.job-card').length === 0) {
                                            location.reload(); // Show empty state
                                        }
                                    }, 300);
                                }
                            });
                });
            });
        </script>
    </body>
</html>
