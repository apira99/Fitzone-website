<?php
session_start();
$host = 'localhost';
$db = 'fitzone';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Add/Edit user logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    if (isset($_POST['edit_id']) && !empty($_POST['edit_id'])) {
        // Update user
        $stmt = $pdo->prepare("UPDATE users SET username=?, email=?, role=? WHERE user_id=?");
        $stmt->execute([$username, $email, $role, $_POST['edit_id']]);
        $_SESSION['message'] = "User updated successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        // Add new user
        $stmt = $pdo->prepare("INSERT INTO users (username, email, role) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $role]);
        $_SESSION['message'] = "User added successfully!";
        $_SESSION['message_type'] = "success";
    }
    header("Location: manage_user.php");
    exit();
}

// Handle Delete User
if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE user_id=?");
        $stmt->execute([$user_id]);
        $_SESSION['message'] = "User deleted successfully!";
        $_SESSION['message_type'] = "success";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error deleting user: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }
    header("Location: manage_user.php");
    exit();
}

// Edit user logic
$user = null;
if (isset($_GET['edit'])) {
    $user_id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id=?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $_SESSION['message'] = "User not found!";
        $_SESSION['message_type'] = "error";
        header("Location: manage_user.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="class.css">
    <style>
        /* Message Bar Styles */
        .message-bar {
            padding: 10px;
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .message-bar.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message-bar.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
    </style>
</head>
<body>
<header>
    <div class="navbar">
        <h1>Admin Dashboard</h1>
        <nav>
            <a href="manage_user.php">Manage User</a>
            <a href="membership.php">Manage Membership</a>
            <a href="trainer.php">Manage Trainers</a>
            <a href="index.html">Logout</a>
        </nav>
    </div>
</header>

<h1 class="centered-title">Manage Staff Users</h1>

<!-- Display message -->
<?php if (isset($_SESSION['message'])): ?>
    <div class="message-bar <?= $_SESSION['message_type'] === 'success' ? 'success' : 'error' ?>">
        <?= $_SESSION['message'] ?>
    </div>
    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
<?php endif; ?>

<!-- User Form (Add/Edit) -->
<form method="POST" action="">
    <input type="hidden" name="edit_id" value="<?= isset($user) ? $user['user_id'] : ''; ?>">
    <label for="username">Username</label>
    <input type="text" name="username" value="<?= isset($user) ? $user['username'] : ''; ?>" required>
    <label for="email">Email</label>
    <input type="email" name="email" value="<?= isset($user) ? $user['email'] : ''; ?>" required>
    <label for="role">Role</label>
    <select name="role">
        <option value="admin" <?= isset($user) && $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
        <option value="member" <?= isset($user) && $user['role'] == 'member' ? 'selected' : ''; ?>>Management Staff</option>
    </select>
    <button type="submit"><?= isset($user) ? 'Update User' : 'Add User' ?></button>
</form>

<!-- Display Users Table -->
<table>
    <thead>
        <tr>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $stmt = $pdo->query("SELECT * FROM users");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>
                <td>{$row['username']}</td>
                <td>{$row['email']}</td>
                <td>{$row['role']}</td>
                <td>
                    <form method='get' action='' style='display:inline;'>
                        <input type='hidden' name='edit' value='{$row['user_id']}'>
                        <button type='submit'>Edit</button>
                    </form>
                    <form method='get' action='' style='display:inline;'>
                        <input type='hidden' name='delete' value='{$row['user_id']}'>
                        <button type='button' class='delete-btn'>Delete</button>
                    </form>
                </td>
            </tr>";
        }
        ?>
    </tbody>
</table>

<!-- Confirmation Dialog Script -->
<div class="overlay"></div>
<div class="confirm-dialog">
    <p>Are you sure you want to delete this user?</p>
    <button class="confirm-yes">Yes</button>
    <button class="confirm-no">No</button>
</div>

<script>
    const deleteBtns = document.querySelectorAll('.delete-btn');
    const confirmDialog = document.querySelector('.confirm-dialog');
    const overlay = document.querySelector('.overlay');
    const confirmYes = document.querySelector('.confirm-yes');
    const confirmNo = document.querySelector('.confirm-no');
    let deleteForm;

    deleteBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            deleteForm = e.target.closest('form');
            confirmDialog.style.display = 'block';
            overlay.style.display = 'block';
        });
    });

    confirmNo.addEventListener('click', () => {
        confirmDialog.style.display = 'none';
        overlay.style.display = 'none';
    });

    confirmYes.addEventListener('click', () => {
        deleteForm.submit();
    });
</script>
</body>
</html>
