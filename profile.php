<?php
session_start();

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'fitzone';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: index.html");
    exit();
}

$email = $_SESSION['user'];

// Fetch client details
$query = "SELECT firstName, lastName, email, phone, membership, class FROM clients WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$client = $result->fetch_assoc();
$stmt->close();

// Check if client details exist
if (!$client) {
    header("Location: error.html");
    exit();
}

// Handle form submission for updating client info
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['updateProfile'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $phone = $_POST['phone'];
    $membership = $_POST['membership'];
    $class = $_POST['class']; // New class field

    $update_query = "UPDATE clients SET firstName = ?, lastName = ?, phone = ?, membership = ?, class = ? WHERE email = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("ssssss", $firstName, $lastName, $phone, $membership, $class, $email);

    if ($update_stmt->execute()) {
        $update_stmt->close();
        header("Location: success.html");
        exit();
    } else {
        $update_stmt->close();
        header("Location: error.html");
        exit();
    }
}

// Handle form submission for making an appointment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['makeAppointment'])) {
    $appointmentDate = $_POST['appointmentDate'];
    $selectedClass = $_POST['class'];

    $appointment_query = "INSERT INTO appointments (client_email, appointment_date, class, status) VALUES (?, ?, ?, 'Pending')";
    $appointment_stmt = $conn->prepare($appointment_query);
    $appointment_stmt->bind_param("sss", $email, $appointmentDate, $selectedClass);

    if ($appointment_stmt->execute()) {
        $appointment_stmt->close();
        header("Location: successappoinment.html");
        exit();
    } else {
        $appointment_stmt->close();
        header("Location: errorappoinment.html");
        exit();
    }
}

// Fetch client's appointment status
$appointments_query = "SELECT * FROM appointments WHERE client_email = ? ORDER BY appointment_date DESC";
$appointments_stmt = $conn->prepare($appointments_query);
$appointments_stmt->bind_param("s", $email);
$appointments_stmt->execute();
$appointments_result = $appointments_stmt->get_result();
$appointments_stmt->close();

// Fetch available classes for the appointment form
$classes_query = "SELECT class_name FROM classes";
$classes_result = $conn->query($classes_query);

// Check if there are any classes returned from the query
if ($classes_result->num_rows === 0) {
    echo "No classes available.";
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .profile-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .profile-details label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }

        .profile-details input,
        .profile-details select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 10px 0;
            text-align: center;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            margin-bottom: 20px; 
        }

        .btn:hover {
            background-color: #45a049;
        }

        .appointments {
            margin-top: 40px;
        }

        .appointments table {
            width: 100%;
            border-collapse: collapse;
        }

        .appointments th, .appointments td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .appointments th {
            background-color: #f4f4f4;
        }
    </style>
</head>

<body>
    <div class="profile-container">
        <h1>Client Profile</h1>
        <form method="POST" action="">
            <div class="profile-details">
                <label for="firstName">First Name:</label>
                <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($client['firstName']); ?>" required>

                <label for="lastName">Last Name:</label>
                <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($client['lastName']); ?>" required>

                <label for="email">Email:</label>
                <input type="email" id="email" value="<?php echo htmlspecialchars($client['email']); ?>" readonly>

                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($client['phone']); ?>" required>

                <label for="membership">Membership Plan:</label>
                <select id="membership" name="membership">
                    <option <?php if ($client['membership'] == 'Basic Plan - 20K LKR/month') echo 'selected'; ?> value="Basic Plan - 20K LKR/month">Basic Plan - 20K LKR/month</option>
                    <option <?php if ($client['membership'] == 'Standard Plan - 35K LKR/month') echo 'selected'; ?> value="Standard Plan - 35K LKR/month">Standard Plan - 35K LKR/month</option>
                    <option <?php if ($client['membership'] == 'Premium Plan - 50K LKR/month') echo 'selected'; ?> value="Premium Plan - 50K LKR/month">Premium Plan - 50K LKR/month</option>
                </select>

                <label for="class">Class:</label>
                <select id="class" name="class">
                    <?php while ($class = $classes_result->fetch_assoc()) : ?>
                        <option value="<?php echo htmlspecialchars($class['class_name']); ?>" <?php if ($client['class'] == $class['class_name']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($class['class_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <button type="submit" name="updateProfile" class="btn">Save Changes</button>
            </div>
        </form>

        <!-- Appointment Form -->
        <div class="appointments">
            <h2>Request Appointment</h2>
            <form method="POST" action="">
                <label for="class">Select Class:</label>
                <select id="class" name="class" required>
                    <?php
                    // Reset result pointer and loop to populate the dropdown again
                    $classes_result->data_seek(0);
                    while ($class = $classes_result->fetch_assoc()) :
                    ?>
                        <option value="<?php echo htmlspecialchars($class['class_name']); ?>"><?php echo htmlspecialchars($class['class_name']); ?></option>
                    <?php endwhile; ?>
                </select>

                <label for="appointmentDate">Appointment Date:</label>
                <input type="datetime-local" id="appointmentDate" name="appointmentDate" required>

                <button type="submit" name="makeAppointment" class="btn">Request Appointment</button>
            </form>

            <h2>Your Appointments</h2>
            <table>
                <thead>
                    <tr>
                        <th>Class</th>
                        <th>Appointment Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($appointment = $appointments_result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($appointment['class']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['status']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
    <!-- Logout Button -->
    <form method="POST" action="logout.php">
        <button type="submit" class="btn">Logout</button>
    </form>    </div>
    </div>


</body>
</html>

<?php
$conn->close();
?>
