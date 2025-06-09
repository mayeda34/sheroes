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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $companyName = mysqli_real_escape_string($conn, $_POST['company_name']);
    $contactPerson = mysqli_real_escape_string($conn, $_POST['contact_person']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $description = mysqli_real_escape_string($conn, $_POST['description'] ?? '');
    $website = mysqli_real_escape_string($conn, $_POST['website'] ?? '');

    // Handle logo upload
    $logoPath = null;
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/company_logos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileExt = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array(strtolower($fileExt), $allowedExtensions)) {
            $fileName = 'company_' . $employerId . '_' . time() . '.' . $fileExt;
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['logo']['tmp_name'], $targetPath)) {
                $logoPath = $targetPath;

                // Delete old logo if it exists
                if (!empty($employer['Logo']) && file_exists($employer['Logo'])) {
                    unlink($employer['Logo']);
                }
            }
        } else {
            $_SESSION['error'] = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
        }
    }

    // Update query
    $query = "UPDATE Employer SET 
                CompanyName = '$companyName',
                ContactPerson = '$contactPerson',
                Phone = '$phone',
                Location = '$location',
                CompanyDescription = '$description',
                Website = '$website'";

    if ($logoPath) {
        $query .= ", Logo = '$logoPath'";
    }

    $query .= " WHERE Id = $employerId";

    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = "Profile updated successfully!";
        header("Location: company-profile.php");
        exit();
    } else {
        $_SESSION['error'] = "Error updating profile: " . mysqli_error($conn);
    }
}

// Get employer data
$query = "SELECT * FROM Employer WHERE Id = $employerId";
$result = mysqli_query($conn, $query);
$employer = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Company Profile | sheroes</title>
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
                line-height: 1.7;
            }
            nav {
                background-color:HOTPINK;
                color: #fff;
                padding: 1rem 2rem;
                border-radius: 0;
            }
           nav .navbar-brand {
    color: WHITE;
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
                background-color: hotpink;
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
            .profile-header {
                background-color: HOTPINK;
                color: white;
                padding: 2rem;
                border-radius: 12px;
                margin-bottom: 2rem;
                background-image: linear-gradient(to right, HOTPINK,  HOTPINK);
            }
            .profile-header h1 {
                font-size: 2.2rem;
                font-weight: 600;
                margin-bottom: 1rem;
                display: flex;
                align-items: center;
                gap: 15px;
            }
            .profile-header p {
                font-size: 1.1rem;
                color: rgba(255, 255, 255, 0.9);
                margin-bottom: 0;
            }
            .company-logo {
                width: 180px;
                height: 180px;
                object-fit: cover;
                border-radius: 50%;
                border: 5px solid #fff;
                box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
                transition: all 0.3s ease;
            }
            .company-logo:hover {
                transform: scale(1.05);
                box-shadow: 0 8px 18px rgba(0, 0, 0, 0.2);
            }
            .logo-container {
                position: relative;
                display: inline-block;
                margin-bottom: 2rem;
            }
            .logo-upload-btn {
                position: absolute;
                bottom: 15px;
                right: 15px;
                background: #fff;
                color: HOTPINK;
                border-radius: 50%;
                width: 48px;
                height: 48px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s ease;
                box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
                border: 2px solid #fff;
            }
            .logo-upload-btn:hover {
                background: #f0f0f0;
                transform: scale(1.1);
                box-shadow: 0 5px 10px rgba(0, 0, 0, 0.15);
            }
            .logo-upload-btn i {
                font-size: 1.4rem;
            }
            #logoInput {
                display: none;
            }
            .default-logo {
                font-size: 90px;
                color: #bdc3c7;
                background-color: #f0f0f0;
                border-radius: 50%;
                width: 180px;
                height: 180px;
                display: flex;
                align-items: center;
                justify-content: center;
                border: 5px solid #fff;
                box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            }
            .default-logo i{
                margin-top: 10px;
            }
            .card-body {
                padding: 2rem;
            }
            .card-body h3 {
    font-size: 1.8rem;
    color: hotpink;
    margin-bottom: 1.2rem;
    font-weight: 600;
}
            .card-body p {
                font-size: 1.1rem;
                color: #666;
                margin-bottom: 1rem;
            }
            .btn-outline-primary {
                color: HOTPINK;
                border-color: HOTPINK;
                padding: 0.75rem 1.5rem;
                font-size: 1.1rem;
                border-radius: 8px;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .btn-outline-primary:hover {
                background-color: #f8f8f8;
                color: HOTPINK;
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }
            form {
                margin-top: 2rem;
            }
            form .form-label {
                font-weight: 500;
                color:hotpink;
                margin-bottom: 0.5rem;
                font-size: 1rem;
            }
            form .form-control {
                border-radius: 8px;
                padding: 0.75rem;
                font-size: 1.1rem;
                border-color: #e0e0e0;
                transition: all 0.3s ease;
            }
            form .form-control:focus {
                border-color: HOTPINK;
                box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.15);
            }
            form textarea.form-control {
                min-height: 120px;
                resize: vertical;
            }
            form .btn-primary {
                background-color: HOTPINK;
                border: none;
                padding: 0.75rem 2rem;
                font-size: 1.1rem;
                border-radius: 8px;
                font-weight: 600;
                transition: all 0.3s ease;
                box-shadow: 0 5px 11px rgba(0, 0, 0, 0.1);
                display: flex;
                align-items: center;
                gap: 10px;
                margin-top: 1rem;
            }
            form .btn-primary:hover {
                background-color: HOTPINK;
                transform: translateY(-2px);
                box-shadow: 0 7px 15px rgba(0, 0, 0, 0.15);
            }
            .col-md-4{
                margin-bottom: 2rem;
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
            <div class="profile-header">
                <h1><i class="fas fa-building"></i> Company Profile</h1>
                <p>Manage your company information</p>
            </div>

            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['message'];
                    unset($_SESSION['message']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
<?php endif; ?>

            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="logo-container">
                                <?php if (!empty($employer['Logo'])): ?>
                                    <img src="<?php echo $employer['Logo']; ?>" alt="Company Logo" class="company-logo" id="logoPreview">
<?php else: ?>
                                    <div class="default-logo">
                                        <i class="fas fa-building"></i>
                                    </div>
<?php endif; ?>
                                <div class="logo-upload-btn" onclick="document.getElementById('logoInput').click()">
                                    <i class="fas fa-camera"></i>
                                </div>
                            </div>
                            <h3><?php echo htmlspecialchars($employer['CompanyName']); ?></h3>
                            <p class="text-muted"><?php echo htmlspecialchars($employer['Location']); ?></p>

                            <div class="d-flex justify-content-center mt-3">
<?php if (!empty($employer['Website'])): ?>
                                    <a href="<?php echo htmlspecialchars($employer['Website']); ?>" target="_blank" class="btn btn-outline-primary">
                                        <i class="fas fa-globe"></i> Website
                                    </a>
<?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h3><i class="fas fa-edit"></i> Edit Company Information</h3>
                           
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="company_name" class="form-label">Company Name *</label>
                                        <input type="text" class="form-control" id="company_name" name="company_name" 
                                               value="<?php echo htmlspecialchars($employer['CompanyName']); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="contact_person" class="form-label">Contact Person *</label>
                                        <input type="text" class="form-control" id="contact_person" name="contact_person" 
                                               value="<?php echo htmlspecialchars($employer['ContactPerson']); ?>" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" 
                                               value="<?php echo htmlspecialchars($employer['Email']); ?>" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Phone *</label>
                                        <input type="text" class="form-control" id="phone" name="phone" 
                                               value="<?php echo htmlspecialchars($employer['Phone']); ?>" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="location" class="form-label">Location *</label>
                                    <input type="text" class="form-control" id="location" name="location" 
                                           value="<?php echo htmlspecialchars($employer['Location']); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="website" class="form-label">Website</label>
                                    <input type="url" class="form-control" id="website" name="website" 
                                           value="<?php echo htmlspecialchars($employer['Website'] ?? ''); ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Company Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="4"><?php
echo htmlspecialchars($employer['CompanyDescription'] ?? '');
?></textarea>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
                                    function previewLogo(event) {
                                        const reader = new FileReader();
                                        reader.onload = function () {
                                            const preview = document.getElementById('logoPreview');
                                            if (preview) {
                                                preview.src = reader.result;
                                            } else {
                                                // Replace default icon with image preview
                                                const logoContainer = document.querySelector('.logo-container');
                                                const defaultLogo = document.querySelector('.default-logo');
                                                if (defaultLogo) {
                                                    defaultLogo.remove();
                                                }
                                                logoContainer.innerHTML = `
                            <img src="${reader.result}" alt="Company Logo" class="company-logo" id="logoPreview">
                            <div class="logo-upload-btn" onclick="document.getElementById('logoInput').click()">
                                <i class="fas fa-camera"></i>
                            </div>
                        `;
                                            }
                                        }
                                        reader.readAsDataURL(event.target.files[0]);
                                    }
        </script>
    </body>
</html>
