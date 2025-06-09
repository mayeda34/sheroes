<?php

include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $education = mysqli_real_escape_string($conn, $_POST['education']);
    $experience = mysqli_real_escape_string($conn, $_POST['experience']);

    // Resume file upload
    $resumeFile = $_FILES['resume']['name'];
    $resumeTmp = $_FILES['resume']['tmp_name'];
    $uploadDir = "uploads/resumes/";

    // Make sure upload folder exists
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $resumePath = $uploadDir . uniqid() . '_' . basename($resumeFile);

    if (move_uploaded_file($resumeTmp, $resumePath)) {
        // Insert into database
        $sql = "INSERT INTO JobSeeker (Name, Email, Password, Phone, Location, ResumeFile, Education, Experience) 
                VALUES ('$name', '$email', '$password', '$phone', '$location', '$resumePath', '$education', '$experience')";

        if (mysqli_query($conn, $sql)) {
            echo "<script>
                alert('Registration successful! You can now login.');
                window.location.href='login.php';
            </script>";
        } else {
            echo "<script>
                alert('Error: " . mysqli_error($conn) . "');
                window.history.back();
            </script>";
        }
    } else {
        echo "<script>
            alert('Resume upload failed. Please try again.');
            window.history.back();
        </script>";
    }

    mysqli_close($conn);
} else {
    echo "<script>
        alert('Invalid request.');
        window.location.href='register.php';
    </script>";
}
?>
