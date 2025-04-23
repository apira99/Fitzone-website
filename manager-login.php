<?php
// Manager default credentials
$manager_email = "manager@gmail.com";
$manager_password = "manager";

// Fetch input from form
$email = $_POST['email'];
$password = $_POST['password'];

// Validate credentials
if ($email === $manager_email && $password === $manager_password) {
    header("Location: manager-dashboard.html"); // Redirect to the manager dashboard
    exit();
} else {
    header("Location: error.php?error=Invalid email or password.");
}
?>
