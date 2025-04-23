<?php
// Set the content type to HTML
header("Content-Type: text/html; charset=UTF-8");

// Database credentials
$host = "localhost";
$db_name = "fitzone";
$username = "root";
$password = "";

try {
    // Create a new PDO connection
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch membership plans from the 'memberships' table
    $membershipPlans = [];
    try {
        $query = "SELECT plan_name, benefits, price FROM memberships";
        $stmt = $conn->query($query);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $membershipPlans[] = $row;
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error loading memberships: " . $e->getMessage() . "');</script>";
    }

    // Check if the form is submitted and validate input
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Sanitize and retrieve the form data
        $firstName = trim($_POST['firstName'] ?? '');
        $lastName = trim($_POST['lastName'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $membership = trim($_POST['membership'] ?? '');
        $class = trim($_POST['class_name'] ?? ''); // Changed to 'class'
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validate required fields
        if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($password) || empty($confirmPassword)) {
            echo "<script>alert('All fields are required.');</script>";
            exit();
        }

        // Check if the passwords match
        if ($password !== $confirmPassword) {
            echo "<script>alert('Error: Passwords do not match.');</script>";
            exit();
        }

        // Hash the password before storing it
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Prepare and execute the SQL query
        $query = "INSERT INTO clients (firstName, lastName, email, phone, membership, class, password) 
                  VALUES (:firstName, :lastName, :email, :phone, :membership, :class, :password)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":firstName", $firstName);
        $stmt->bindParam(":lastName", $lastName);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":phone", $phone);
        $stmt->bindParam(":membership", $membership);
        $stmt->bindParam(":class", $class);  // Changed to 'class'
        $stmt->bindParam(":password", $hashedPassword);

        // Execute the query
        if ($stmt->execute()) {
            header("Location: registration_success.html");
        } else {
            $errorInfo = $stmt->errorInfo();
            echo "<script>alert('Failed to submit data. Error: " . $errorInfo[2] . "');</script>";
        }
    }
} catch (PDOException $e) {
    echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Registration - FitZone Fitness Center</title>
    <link rel="stylesheet" href="clientregister.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <header>
        <div class="navbar">
            <h1>FitZone Fitness Center</h1>
            <nav>
                <a href="#class-section">Classes</a>
                <a href="#membership-plans">Membership plan</a>
                <a href="#queries">Submit query</a>
                <a href="index.html">Home</a>
                <a href="join.html">Login</a>
                
            </nav>
        </div>
    </header>

    <section id="class-section">
    <h2>Available Classes</h2>
    <div class="class-cards">
        <?php
        // Fetch class details from the 'classes' table
        try {
            $query = "SELECT class_name, description FROM classes";
            $stmt = $conn->query($query);

            // Display each class in a card format
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<div class='class-card'>";
                echo "<h3>" . htmlspecialchars($row['class_name']) . "</h3>";
                echo "<p>" . nl2br(htmlspecialchars($row['description'])) . "</p>";
                echo "</div>";
            }
        } catch (PDOException $e) {
            echo "<p>Error loading classes: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>
</section>


    <!-- Membership Plans Section (Card Layout) -->
    <section id="membership-plans">
        <h2 class="centered-title">Choose Your Membership Plan</h2>
        <div class="membership-cards">
            <?php foreach ($membershipPlans as $plan) { ?>
                <div class="membership-card">
                    <h3><?php echo htmlspecialchars($plan['plan_name']); ?></h3>
                    <p><?php echo nl2br(htmlspecialchars($plan['benefits'])); ?></p>
                    <p class="price"><?php echo number_format($plan['price'], 2) . " LKR/month"; ?></p>
                </div>
            <?php } ?>
        </div>
    </section>

    <!-- Submit Queries Section -->
<section id="queries" class="content">
    <h2>Submit Your Queries</h2>
    <form action="submit_query.php" method="POST" class="query-form">
        <label for="name">Your Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Your Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="query">Your Query:</label>
        <textarea id="query" name="query" rows="5" required></textarea>

        <button type="submit">Submit</button>
    </form>
</section>

    <!-- Registration Form Section -->
    <section id="register">
        <h2>Register for FitZone</h2>
        <form action="" method="POST">
            <!-- Basic Contact Details -->
            <div class="form-group">
                <label for="firstName">First Name:</label>
                <input type="text" id="firstName" name="firstName" required>
            </div>
            <div class="form-group">
                <label for="lastName">Last Name:</label>
                <input type="text" id="lastName" name="lastName" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number:</label>
                <input type="tel" id="phone" name="phone" required>
            </div>

            <!-- Class Selection -->
            <div class="form-group">
                <label for="class_name">Select Class:</label>
                <select id="class_name" name="class_name" required>
                    <option value="">--Choose a Class--</option>
                    <?php
                    // Fetch class names from the 'classes' table
                    try {
                        $query = "SELECT class_name FROM classes";
                        $stmt = $conn->query($query);

                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='" . htmlspecialchars($row['class_name']) . "'>" . htmlspecialchars($row['class_name']) . "</option>";
                        }
                    } catch (PDOException $e) {
                        echo "<script>alert('Error loading classes: " . $e->getMessage() . "');</script>";
                    }
                    ?>
                </select>
            </div>

            <!-- Membership Plan Selection -->
            <div class="form-group">
                <label for="membership">Select Membership Plan:</label>
                <select id="membership" name="membership" required>
                    <option value="">--Choose a Plan--</option>
                    <?php foreach ($membershipPlans as $plan) { ?>
                        <option value="<?php echo htmlspecialchars($plan['plan_name']); ?>"><?php echo htmlspecialchars($plan['plan_name']); ?></option>
                    <?php } ?>
                </select>
            </div>

            <!-- Password Fields -->
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <!-- Submit Button -->
            <button type="submit">Register</button>
        </form>
    </section>
</body>
</html>
