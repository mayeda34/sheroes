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
$message = '';
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $education = mysqli_real_escape_string($conn, $_POST['education']);
    $experience = mysqli_real_escape_string($conn, $_POST['experience']);

    // Handle resume upload if provided
    $resumePath = null;
    if (!empty($_FILES['resume']['name'])) {
        $resumeFile = $_FILES['resume']['name'];
        $resumeTmp = $_FILES['resume']['tmp_name'];
        $uploadDir = "uploads/resumes/";
        
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $resumePath = $uploadDir . uniqid() . '_' . basename($resumeFile);
        
        if (!move_uploaded_file($resumeTmp, $resumePath)) {
            $message = "Error uploading resume file.";
        }
    }

    // Update query
    $query = "UPDATE JobSeeker SET 
                Name = '$name', 
                Phone = '$phone', 
                Location = '$location', 
                Education = '$education', 
                Experience = '$experience'";
    
    if ($resumePath) {
        $query .= ", ResumeFile = '$resumePath'";
    }
    
    $query .= " WHERE Id = $jobSeekerId";
    
    if (mysqli_query($conn, $query)) {
        // Update session name if changed
        $_SESSION['name'] = $name;
        $message = "Profile updated successfully!";
        $success = true;
    } else {
        $message = "Error updating profile: " . mysqli_error($conn);
    }
}

// Get current profile data
$query = "SELECT * FROM JobSeeker WHERE Id = $jobSeekerId";
$result = mysqli_query($conn, $query);
$profile = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | sheroes</title>
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
        .profile-header {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            text-align: center;
        }
        .profile-pic {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid #e0f7fa;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
            margin-bottom: 20px;
        }
        .profile-name {
            font-size: 2rem;
            font-weight: 600;
            color:HOTPINK;
            margin-bottom: 10px;
        }
        .profile-email {
            font-size: 1.1rem;
            color: #7f8c8d;
            margin-bottom: 0;
        }
        .edit-profile-btn {
            background-color:HOTPINK;
            color: white;
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 500;
            transition: background-color 0.3s ease, transform 0.2s ease;
            border: none;
            box-shadow: 0 3px 7px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .edit-profile-btn:hover {
            background-color:HOTPINK;
            transform: translateY(-2px);
        }
        .form-label {
            font-weight: 500;
            color:HOTPINK;
            margin-bottom: 8px;
        }
        .form-control {
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            padding: 12px;
            transition: all 0.3s ease;
            font-size: 1rem;
        }
        .form-control:focus {
            border-color:HOTPINK;
            box-shadow:HOTPINK;
            outline: none;
        }
        .textarea-control {
            height: auto;
            min-height: 120px;
            resize: vertical;
        }
        .card {
            background-color: #ffffff;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border: none;
        }
        .card-header {
            background-color: #f7fafc;
            border-bottom: 2px solid #e0e0e0;
            padding: 20px;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }
        .card-header h5 {
            font-size: 1.5rem;
            font-weight: 600;
            color:HOTPINK;
            margin-bottom: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .card-body {
            padding: 25px;
        }
        .resume-preview {
            border: 2px dashed #a0aec0;
            padding: 20px;
            border-radius: 8px;
            background-color: #edf2f7;
            margin-bottom: 20px;
            text-align: center;
        }
        .resume-preview h6 {
            font-size: 1.1rem;
            color:HOTPINK;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .upload-btn {
            background-color:HOTPINK;
            color: white;
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 500;
            transition: background-color 0.3s ease, transform 0.2s ease;
            border: none;
            box-shadow: 0 3px 7px rgba(0, 0, 0, 0.1);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .upload-btn:hover {
            background-color:HOTPINK;
            transform: translateY(-2px);
        }
        .save-profile-btn {
            background-color:HOTPINK;
            color: white;
            border-radius: 8px;
            padding: 14px 32px;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.2s ease;
            border: none;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            margin-top: 30px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .save-profile-btn:hover {
            background-color:HOTPINK;
            transform: translateY(-2px);
            box-shadow: 0 5px 12px rgba(0, 0, 0, 0.2);
        }
        .alert {
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 1rem;
        }
        .alert-success {
            background-color: #f0fdf4;
            color:HOTPINK;
            border-color: #d6f4e5;
        }
        .alert-danger {
            background-color: #fee2e2;
            color:HOTPINK;
            border-color: #fecaca;
        }
        
         .navbar {
            background-color: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            padding: 1rem 0;
        }

        .navbar-brand {
            color:HOTPINK;
            font-weight: 600;
            font-size: 1.8rem;
        }

        .navbar-nav .nav-link {
            color: HOTPINK;
            margin-right: 15px;
            font-weight: 500;
            transition: color 0.3s ease, transform 0.2s ease;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color:HOTPINK;
            transform: translateY(-2px);
        }
        .btn-outline-primary {
    --bs-btn-color: HOTPINK;
    --bs-btn-border-color: HOTPINK;
    --bs-btn-hover-color: #fff;
    --bs-btn-hover-bg: #0d6efd;
    --bs-btn-hover-border-color: #0d6efd;
    --bs-btn-focus-shadow-rgb: 13,110,253;
    --bs-btn-active-color: #fff;
    --bs-btn-active-bg: #0d6efd;
    --bs-btn-active-border-color: #0d6efd;
    --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
    --bs-btn-disabled-color: #0d6efd;
    --bs-btn-disabled-bg: transparent;
    --bs-btn-disabled-border-color: #0d6efd;
    --bs-gradient: none;
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
                <div class="profile-header">
                    <div class="d-flex justify-content-center mb-4">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($profile['Name']) ?>&size=150&background=0c6381&color=fff"
                             alt="Profile Picture" class="profile-pic">
                    </div>
                    <h2 class="profile-name"><?= htmlspecialchars($profile['Name']) ?></h2>
                    <p class="profile-email"><i class="fas fa-envelope"></i> <?= htmlspecialchars($profile['Email']) ?></p>
                </div>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?= $success ? 'success' : 'danger' ?>"><?= $message ?></div>
                <?php endif; ?>

                <form method="POST" action="profile.php" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-user"></i> Basic Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($profile['Name']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" value="<?= htmlspecialchars($profile['Email']) ?>" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Phone Number</label>
                                        <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($profile['Phone']) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Location</label>
                                        <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($profile['Location']) ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-graduation-cap"></i> Education & Experience</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Education</label>
                                        <textarea name="education" class="form-control textarea-control" rows="3"><?= htmlspecialchars($profile['Education']) ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Work Experience</label>
                                        <textarea name="experience" class="form-control textarea-control" rows="3"><?= htmlspecialchars($profile['Experience']) ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5><i class="fas fa-file-alt"></i> Resume</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($profile['ResumeFile'])): ?>
                                <div class="resume-preview mb-3">
                                    <h6>Current Resume:</h6>
                                    <a href="<?= htmlspecialchars($profile['ResumeFile']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-download"></i> Download Resume
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <label class="form-label">Upload New Resume (PDF or DOCX)</label>
                                <input type="file" name="resume" class="form-control" accept=".pdf,.doc,.docx">
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn save-profile-btn"><i class="fas fa-save"></i> Save Profile</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
