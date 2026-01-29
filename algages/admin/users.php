<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
check_login();
check_super_admin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_user'])) {
        $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
            $stmt->execute([$_POST['username'], $hash, $_POST['role']]);
            $message = "User added successfully.";
        } catch (PDOException $e) {
            $message = "Error: Username might already exist.";
        }
    } elseif (isset($_POST['delete_user'])) {
        if ($_POST['id'] != $_SESSION['user_id']) {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $message = "User deleted successfully.";
        } else {
            $message = "You cannot delete yourself.";
        }
    }
}

$stmt = $pdo->query("SELECT id, username, role FROM users");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management - Algages</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        nav { background: var(--dark-gray); padding: 1rem; margin-bottom: 2rem; }
        nav a { color: white; text-decoration: none; margin-right: 1.5rem; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--light-gray); }
        .form-inline { display: flex; gap: 0.5rem; margin-bottom: 2rem; background: white; padding: 1.5rem; border-radius: 8px; }
        .form-inline input, .form-inline select { padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; }
    </style>
</head>
<body>
    <header>
        <h1>USER MANAGEMENT</h1>
    </header>
    <nav>
        <div class="container" style="margin:0 auto">
            <a href="dashboard.php">Inquiry Inbox</a>
            <a href="services.php">Service Manager</a>
            <a href="users.php">User Management</a>
            <a href="logout.php" style="float:right">Logout</a>
        </div>
    </nav>

    <div class="container">
        <?php if ($message): ?>
            <div style="background: #d4edda; color: #155724; padding: 1rem; margin-bottom: 1rem; border-radius: 4px;"><?php echo $message; ?></div>
        <?php endif; ?>

        <h3>Add New User</h3>
        <form method="POST" class="form-inline">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="role">
                <option value="viewer">Viewer</option>
                <option value="editor">Editor</option>
                <option value="super_admin">Super Admin</option>
            </select>
            <button type="submit" name="add_user" class="btn btn-orange">Add User</button>
        </form>

        <h3>System Users</h3>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><?php echo htmlspecialchars($u['username']); ?></td>
                    <td><?php echo $u['role']; ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                            <button type="submit" name="delete_user" class="btn btn-orange" style="background: #c0392b; padding: 0.3rem 0.6rem; font-size: 0.7rem;" onclick="return confirm('Delete user?')">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
