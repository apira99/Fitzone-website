<?php
// Manager default credentials
$manager_email = "admin@gmail.com";
$manager_password = "admin";

// Fetch input from form
$email = $_POST['email'];
$password = $_POST['password'];

// Validate credentials
if ($email === $manager_email && $password === $manager_password) {
    header("Location:admin_dashboard.html"); // Redirect to the manager dashboard
    exit();
} else {
    header("Location: error.php?error=Invalid email or password.");}
?>
