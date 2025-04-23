<?php
if (isset($_GET['error'])) {
    $error_message = $_GET['error'];
} else {
    $error_message = "An unexpected error occurred.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - FitZone Fitness Center</title>
    <link rel="stylesheet" href="error.css">
</head>

<body>
    <div class="error-container">
        <h1 class="error-title">Oops! Something Went Wrong.</h1>
        <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
        <a href="join.html" class="error-link">Go back to login</a>
    </div>
</body>

</html>
