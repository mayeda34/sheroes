<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include 'config.php';

if (!isset($_GET['id']) || !isset($_GET['type'])) {
    header("Location: manage-users.php");
    exit();
}

$id = intval($_GET['id']);
$type = $_GET['type'];

if ($type == 'jobseeker') {
    $table = "JobSeeker";
} elseif ($type == 'employer') {
    $table = "Employer";
} else {
    header("Location: manage-users.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $location = $_POST['location'];

    if ($type == 'jobseeker') {
        $query = "UPDATE JobSeeker SET Name='$name', Email='$email', Phone='$phone', Location='$location' WHERE Id=$id";
    } else {
        $contactPerson = mysqli_real_escape_string($conn, $_POST['contact_person']);
        $query = "UPDATE Employer SET CompanyName='$name', ContactPerson='$contactPerson', Email='$email', Phone='$phone', Location='$location' WHERE Id=$id";
    }

    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = "User updated successfully.";
        header("Location: manage-users.php");
        exit();
    } else {
        $error = "Failed to update user.";
    }
}

// Fetch user details
$query = "SELECT * FROM $table WHERE Id = $id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User | sheroes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(to right,hotpink);
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
        }
        h3 {
    color: hotpink;
}

        .card {
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            animation: fadeInUp 0.6s ease-in-out;
            background-color: #fff;
        }

        .card-header {
            background-color: #fff;
            border-bottom: 2px solid #f1f1f1;
        }

        .form-label {
            font-weight: 600;
            color: #333;
        }

        .btn-save, .btn-back {
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 8px;
        }

        .btn-save {
            background-color:hotpink;
            color: white;
        }

        .btn-save:hover {
            background-color:hotpink;
        }

        .btn-back {
            background-color:hotpink;
            color: white;
        }

        .btn-back:hover {
            background-color:hotpink;
        }

        .alert {
            border-radius: 8px;
            font-weight: bold;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center mt-5">
    <div class="card w-75 p-4">
        <div class="card-header text-center">
            <h3><i class="fas fa-edit me-2"></i>Edit User</h3>
        </div>
        <div class="card-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label"><?php echo $type == 'jobseeker' ? 'Name' : 'Company Name'; ?></label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($type == 'jobseeker' ? $user['Name'] : $user['CompanyName']); ?>" required>
                </div>

                <?php if ($type == 'employer'): ?>
                    <div class="mb-3">
                        <label class="form-label">Contact Person</label>
                        <input type="text" name="contact_person" class="form-control" value="<?php echo htmlspecialchars($user['ContactPerson']); ?>" required>
                    </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['Email']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['Phone']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($user['Location']); ?>" required>
                </div>

                <button type="submit" class="btn btn-save">
                    <i class="fas fa-save me-2"></i> Save Changes
                </button>
                <a href="manage-users.php" class="btn btn-back">
                    <i class="fas fa-arrow-left me-2"></i> Back
                </a>
            </form>
        </div>
    </div>
</div>

</body>
</html>
