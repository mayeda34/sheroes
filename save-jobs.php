<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] != 'jobseeker') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

include 'config.php';

$jobSeekerId = $_SESSION['user_id'];
$jobId = isset($_POST['job_id']) ? intval($_POST['job_id']) : 0;
$action = isset($_POST['action']) ? $_POST['action'] : '';

// Validate job exists
$jobCheck = mysqli_query($conn, "SELECT Id FROM Job WHERE Id = $jobId");
if (mysqli_num_rows($jobCheck) == 0) {
    echo json_encode(['success' => false, 'message' => 'Job not found']);
    exit();
}

// Check if SavedJobs table exists, create if not
$tableCheck = mysqli_query($conn, "SHOW TABLES LIKE 'SavedJobs'");
if (mysqli_num_rows($tableCheck) == 0) {
    $createTable = "CREATE TABLE SavedJobs (
        Id INT AUTO_INCREMENT PRIMARY KEY,
        JobSeekerId INT NOT NULL,
        JobId INT NOT NULL,
        SavedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (JobSeekerId) REFERENCES JobSeeker(Id) ON DELETE CASCADE,
        FOREIGN KEY (JobId) REFERENCES Job(Id) ON DELETE CASCADE,
        UNIQUE KEY unique_save (JobSeekerId, JobId)
    )";
    
    if (!mysqli_query($conn, $createTable)) {
        echo json_encode(['success' => false, 'message' => 'Failed to initialize saved jobs']);
        exit();
    }
}

if ($action == 'save') {
    // Check if already saved
    $checkQuery = "SELECT Id FROM SavedJobs WHERE JobSeekerId = $jobSeekerId AND JobId = $jobId";
    $alreadySaved = mysqli_num_rows(mysqli_query($conn, $checkQuery)) > 0;
    
    if (!$alreadySaved) {
        $query = "INSERT INTO SavedJobs (JobSeekerId, JobId) VALUES ($jobSeekerId, $jobId)";
        if (mysqli_query($conn, $query)) {
            echo json_encode(['success' => true, 'action' => 'saved']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: '.mysqli_error($conn)]);
        }
    } else {
        echo json_encode(['success' => true, 'action' => 'already_saved']);
    }
} elseif ($action == 'unsave') {
    $query = "DELETE FROM SavedJobs WHERE JobSeekerId = $jobSeekerId AND JobId = $jobId";
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'action' => 'unsaved']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: '.mysqli_error($conn)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>