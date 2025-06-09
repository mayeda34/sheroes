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
    $query = "SELECT * FROM JobSeeker WHERE Id = $id";
} elseif ($type == 'employer') {
    $query = "SELECT * FROM Employer WHERE Id = $id";
} else {
    header("Location: manage-users.php");
    exit();
}

$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    $_SESSION['message'] = "User not found.";
    header("Location: manage-users.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View User | sheroes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(to right,hotpink,hotpink);
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            animation: fadeInUp 0.5s ease-in-out;
        }

        .card-header {
            background-color: #fff;
            border-bottom: 2px solid #f1f1f1;
        }

        .card-body table th {
            width: 30%;
            background-color: #f8f9fa;
        }
                  h3 {
    color: hotpink;
}

        .btn-back {
            background-color: hotpink;
            color: #fff;
        }

        .btn-back:hover {
            background-color:hotpink;
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
    <div class="card w-75">
        <div class="card-header text-center">
            <h3><i class="fas fa-user-circle me-2"></i>User Details</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <?php foreach ($user as $key => $value): ?>
                    <tr>
                        <th><i class="fas fa-angle-right me-1 text-primary"></i><?php echo ucfirst(str_replace("_", " ", $key)); ?></th>
                        <td><?php echo htmlspecialchars($value); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <div class="text-end">
                <a href="manage-users.php" class="btn btn-back mt-3">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>
</div>

</body>
</html>
