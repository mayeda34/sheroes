<?php

include 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Determine which table to query based on role
    $table = '';
    switch ($role) {
        case 'admin':
            $table = 'Admin';
            break;
        case 'employer':
            $table = 'Employer';
            break;
        case 'jobseeker':
            $table = 'JobSeeker';
            break;
        default:
            echo "<script>
                alert('Invalid role selected.');
                window.location.href='login.php';
            </script>";
            exit();
    }

    // Query the database
    $sql = "SELECT * FROM $table WHERE Email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        // Verify password
        if (password_verify($password, $user['Password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['Id'];
            $_SESSION['email'] = $user['Email'];
            $_SESSION['role'] = $role;

            // Additional session variables based on role
            if ($role == 'admin') {
                $_SESSION['name'] = $user['Name'];
                header("Location: admin-dashboard.php");
            } elseif ($role == 'employer') {
                $_SESSION['company_name'] = $user['CompanyName'];
                $_SESSION['contact_person'] = $user['ContactPerson'];
                header("Location: employer-dashboard.php");
            } elseif ($role == 'jobseeker') {
                $_SESSION['name'] = $user['Name'];
                $_SESSION['resume'] = $user['ResumeFile'];
                header("Location: jobseeker-dashboard.php");
            }
            exit();
        } else {
            echo "<script>
                alert('Invalid password.');
                window.history.back();
            </script>";
        }
    } else {
        echo "<script>
            alert('Email not found.');
            window.history.back();
        </script>";
    }

    mysqli_close($conn);
} else {
    echo "<script>
        alert('Invalid request.');
        window.location.href='login.php';
    </script>";
}
?>