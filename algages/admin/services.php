<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
check_login();

$message = '';

// Handle CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_service'])) {
        $stmt = $pdo->prepare("INSERT INTO services (category_id, title, description, base_price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_POST['category_id'], $_POST['title'], $_POST['description'], $_POST['base_price']]);
        $message = "Service added successfully.";
    } elseif (isset($_POST['update_service'])) {
        $stmt = $pdo->prepare("UPDATE services SET category_id = ?, title = ?, description = ?, base_price = ? WHERE id = ?");
        $stmt->execute([$_POST['category_id'], $_POST['title'], $_POST['description'], $_POST['base_price'], $_POST['id']]);
        $message = "Service updated successfully.";
    } elseif (isset($_POST['delete_service'])) {
        $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $message = "Service deleted successfully.";
    }
}

$stmt = $pdo->query("SELECT s.*, c.name as category_name FROM services s JOIN categories c ON s.category_id = c.id ORDER BY c.sort_order, s.title");
$services = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories ORDER BY sort_order")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Service Manager - Algages</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        nav { background: var(--dark-gray); padding: 1rem; margin-bottom: 2rem; }
        nav a { color: white; text-decoration: none; margin-right: 1.5rem; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--light-gray); }
        .form-inline { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 2rem; background: white; padding: 1.5rem; border-radius: 8px; }
        .form-inline input, .form-inline select, .form-inline textarea { padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; }
    </style>
</head>
<body>
    <header>
        <h1>SERVICE MANAGER</h1>
    </header>
    <nav>
        <div class="container" style="margin:0 auto">
            <a href="dashboard.php">Inquiry Inbox</a>
            <a href="services.php">Service Manager</a>
            <?php if ($_SESSION['role'] === 'super_admin'): ?>
                <a href="users.php">User Management</a>
            <?php endif; ?>
            <a href="logout.php" style="float:right">Logout</a>
        </div>
    </nav>

    <div class="container">
        <?php if ($message): ?>
            <div style="background: #d4edda; color: #155724; padding: 1rem; margin-bottom: 1rem; border-radius: 4px;"><?php echo $message; ?></div>
        <?php endif; ?>

        <h3>Add New Service</h3>
        <form method="POST" class="form-inline">
            <select name="category_id" required>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="title" placeholder="Service Title" required style="flex: 1;">
            <input type="number" step="0.01" name="base_price" placeholder="Base Price" required style="width: 100px;">
            <textarea name="description" placeholder="Description" style="flex: 1; min-width: 200px; height: 38px;"></textarea>
            <button type="submit" name="add_service" class="btn btn-orange">Add</button>
        </form>

        <h3>Existing Services</h3>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Title</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($services as $s): ?>
                <tr>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?php echo $s['id']; ?>">
                        <td>
                            <select name="category_id">
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo $s['category_id'] == $cat['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><input type="text" name="title" value="<?php echo htmlspecialchars($s['title']); ?>" style="width: 90%;"></td>
                        <td><input type="number" step="0.01" name="base_price" value="<?php echo $s['base_price']; ?>" style="width: 80px;"></td>
                        <td>
                            <textarea name="description" style="width: 150px; height: 30px;"><?php echo htmlspecialchars($s['description']); ?></textarea>
                        </td>
                        <td>
                            <button type="submit" name="update_service" class="btn btn-orange" style="padding: 0.3rem 0.6rem; font-size: 0.7rem;">Update</button>
                            <button type="submit" name="delete_service" class="btn btn-orange" style="padding: 0.3rem 0.6rem; font-size: 0.7rem; background: #c0392b;" onclick="return confirm('Delete this service?')">Del</button>
                        </td>
                    </form>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
