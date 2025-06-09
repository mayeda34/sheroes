<?php
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Determine role-specific sidebar items
$role = $_SESSION['role'];
?>

<div class="col-md-3 col-lg-2 sidebar p-0">
    <div class="p-4">
        <h4 class="text-center mb-4">SHEROES</h4>
        <hr>
        <ul class="nav flex-column">
            <?php if ($role == 'employer'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'employer-dashboard.php' ? 'active' : ''; ?>" href="employer-dashboard.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'post-job.php' ? 'active' : ''; ?>" href="post-job.php">
                        <i class="fas fa-plus-circle"></i> Post New Job
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage-posted-jobs.php' ? 'active' : ''; ?>" href="manage-posted-jobs.php">
                        <i class="fas fa-briefcase"></i> My Job Postings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'applications.php' ? 'active' : ''; ?>" href="applications.php">
                        <i class="fas fa-file-alt"></i> Applications
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'company-profile.php' ? 'active' : ''; ?>" href="company-profile.php">
                        <i class="fas fa-building"></i> Company Profile
                    </a>
                </li>
            <?php elseif ($role == 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'admin-dashboard.php' ? 'active' : ''; ?>" href="admin-dashboard.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage-users.php' ? 'active' : ''; ?>" href="manage-users.php">
                        <i class="fas fa-users"></i> Manage Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage-jobs.php' ? 'active' : ''; ?>" href="manage-jobs.php">
                        <i class="fas fa-briefcase"></i> Manage Jobs
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>" href="reports.php">
                        <i class="fas fa-chart-bar"></i> Reports
                    </a>
                </li>
            <?php endif; ?>
            <li class="nav-item mt-4">
                <a class="nav-link text-danger" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</div>