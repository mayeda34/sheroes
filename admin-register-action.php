<?php

include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $checkEmail = "SELECT * FROM Admin WHERE Email = '$email'";
    $result = mysqli_query($conn, $checkEmail);

    if (mysqli_num_rows($result) > 0) {
        echo "<script>
            alert('Email already registered. Please use a different email.');
            window.history.back();
        </script>";
    } else {
        // Insert into database
        $sql = "INSERT INTO Admin (Name, Email, Password) 
                VALUES ('$name', '$email', '$password')";

        if (mysqli_query($conn, $sql)) {
            echo "<script>
                alert('Admin registration successful! You can now login.');
                window.location.href='login.php';
            </script>";
        } else {
            echo "<script>
                alert('Error: " . mysqli_error($conn) . "');
                window.history.back();
            </script>";
        }
    }

    mysqli_close($conn);
} else {
    echo "<script>
        alert('Invalid request.');
        window.location.href='register.php';
    </script>";
}
?>