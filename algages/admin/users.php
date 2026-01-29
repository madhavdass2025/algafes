<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
check_login();
check_super_admin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token'] ?? '');

    if (isset($_POST['add_user'])) {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = mysqli_real_escape_string($conn, $_POST['role']);

        $sql = "INSERT INTO users (username, password_hash, role) VALUES ('$username', '$hash', '$role')";
        if (mysqli_query($conn, $sql)) {
            $message = "User added successfully.";
        } else {
            $message = "Error: Username might already exist.";
        }
    } elseif (isset($_POST['update_user'])) {
        $id = (int)$_POST['id'];
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $role = mysqli_real_escape_string($conn, $_POST['role']);

        if (!empty($_POST['password'])) {
            $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $sql = "UPDATE users SET username = '$username', password_hash = '$hash', role = '$role' WHERE id = $id";
        } else {
            $sql = "UPDATE users SET username = '$username', role = '$role' WHERE id = $id";
        }

        if (mysqli_query($conn, $sql)) {
            $message = "User updated successfully.";
        } else {
            $message = "Error updating user.";
        }
    } elseif (isset($_POST['delete_user'])) {
        $id = (int)$_POST['id'];
        if ($id != $_SESSION['user_id']) {
            $sql = "DELETE FROM users WHERE id = $id";
            mysqli_query($conn, $sql);
            $message = "User deleted successfully.";
        } else {
            $message = "You cannot delete yourself.";
        }
    }
}

$sql = "SELECT id, username, role FROM users";
$result = mysqli_query($conn, $sql);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);
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
        .form-inline { display: flex; gap: 0.5rem; margin-bottom: 2rem; background: white; padding: 1.5rem; border-radius: 8px; flex-wrap: wrap; }
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
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
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
                    <th>New Password (optional)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                        <td><input type="text" name="username" value="<?php echo htmlspecialchars($u['username']); ?>" required></td>
                        <td>
                            <select name="role">
                                <option value="viewer" <?php echo $u['role'] === 'viewer' ? 'selected' : ''; ?>>Viewer</option>
                                <option value="editor" <?php echo $u['role'] === 'editor' ? 'selected' : ''; ?>>Editor</option>
                                <option value="super_admin" <?php echo $u['role'] === 'super_admin' ? 'selected' : ''; ?>>Super Admin</option>
                            </select>
                        </td>
                        <td><input type="password" name="password" placeholder="Leave blank to keep current"></td>
                        <td>
                            <button type="submit" name="update_user" class="btn btn-orange" style="padding: 0.3rem 0.6rem; font-size: 0.7rem;">Update</button>
                            <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                <button type="submit" name="delete_user" class="btn btn-orange" style="background: #c0392b; padding: 0.3rem 0.6rem; font-size: 0.7rem;" onclick="return confirm('Delete user?')">Delete</button>
                            <?php endif; ?>
                        </td>
                    </form>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
