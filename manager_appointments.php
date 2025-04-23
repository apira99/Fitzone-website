<?php
session_start(); // Start the session at the top of the file

// Database connection parameters
$host = 'localhost'; // Change if not running locally
$db = 'fitzone';     // Replace with your database name
$user = 'root';      // Replace with your DB username
$pass = '';          // Replace with your DB password

// Create a new connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all appointments from the database
$query = "SELECT appointments.id, clients.firstName, clients.lastName, appointments.class, appointments.appointment_date, appointments.status 
          FROM appointments 
          INNER JOIN clients ON appointments.client_email = clients.email";
$result = $conn->query($query);

// Update the status of an appointment
if (isset($_POST['updateStatus'])) {
    $appointmentId = $_POST['appointmentId'];
    $newStatus = $_POST['status'];

    $updateQuery = "UPDATE appointments SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("si", $newStatus, $appointmentId);
    $stmt->execute();

    // Redirect to the same page to see changes
    header("Location: manager_appointments.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointments</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

       

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
            color: #333;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        select {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }

        button {
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .btn {
            padding: 8px 16px;
            background-color: #008CBA;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
        }

        .btn:hover {
            background-color: #007B9A;
        }

        form {
            display: inline;
        }

        /* Navigation Bar */
    header {
        background: #333;
        color: #fff;
        padding: 1rem 0;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 20px;
    }
    
    .navbar h1 {
        font-size: 1.5rem;
    }
    
    .navbar nav a {
        color: #fff;
        text-decoration: none;
        margin: 0 10px;
    }
    
    .navbar nav a:hover {
        text-decoration: underline;
    }
    
    .centered-title {
        text-align: center;
    }

    /* Dashboard Styles */
.dashboard {
    margin: 20px;
    padding: 20px;
    background-color: #f4f4f4;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.dashboard h2 {
    font-size: 2rem;
    margin-bottom: 10px;
    color: #333;
}

.dashboard p {
    font-size: 1.1rem;
    color: #555;
}
    </style>
</head>
<body>
<header>
        <div class="navbar">
            <h1>Manager Dashboard</h1>
            <nav>
                <a href="class.php">Manage classes</a>
                <a href="manager_appointments.php" >Manage appoinments</a>
                <a href="respond-queries.html" >Respond to Queries</a>
                <a href="index.html">Logout</a>
            </nav>
        </div>
    </header>
    <div class="container">
        <h1>Manage Appointments</h1>
        <table>
            <thead>
                <tr>
                    <th>Client Name</th>
                    <th>Class</th>
                    <th>Appointment Date</th>
                    <th>Status</th>
                    <th>Update Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['firstName'] . ' ' . $row['lastName']); ?></td>
                        <td><?php echo htmlspecialchars($row['class']); ?></td>
                        <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td>
                            <form method="POST" action="manager_appointments.php">
                                <input type="hidden" name="appointmentId" value="<?php echo $row['id']; ?>">
                                <select name="status">
                                    <option <?php if ($row['status'] == 'Pending') echo 'selected'; ?> value="Pending">Pending</option>
                                    <option <?php if ($row['status'] == 'Confirmed') echo 'selected'; ?> value="Confirmed">Confirmed</option>
                                    <option <?php if ($row['status'] == 'Cancelled') echo 'selected'; ?> value="Cancelled">Cancelled</option>
                                    <option <?php if ($row['status'] == 'Completed') echo 'selected'; ?> value="Completed">Completed</option>
                                </select>
                                <button type="submit" name="updateStatus" class="btn">Update Status</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
