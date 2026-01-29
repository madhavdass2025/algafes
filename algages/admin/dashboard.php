<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
check_login();

$sql = "SELECT * FROM client_submissions ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
$submissions = mysqli_fetch_all($result, MYSQLI_ASSOC);

$sql_count = "SELECT COUNT(*) FROM client_submissions WHERE status = 'new'";
$result_count = mysqli_query($conn, $sql_count);
$new_count = mysqli_fetch_row($result_count)[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Algages</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        nav { background: var(--dark-gray); padding: 1rem; margin-bottom: 2rem; }
        nav a { color: white; text-decoration: none; margin-right: 1.5rem; font-weight: bold; }
        nav a:hover { color: var(--prof-orange); }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--light-gray); }
        th { background: var(--slate-gray); color: white; }
        tr:hover { background: #f9f9f9; }
        .status-badge { padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: bold; text-transform: uppercase; }
        .status-new { background: #e67e22; color: white; }
        .status-in-progress { background: #3498db; color: white; }
        .status-completed { background: #27ae60; color: white; }
        @keyframes pulse {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(230, 126, 34, 0.7); }
            70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(230, 126, 34, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(230, 126, 34, 0); }
        }
    </style>
</head>
<body>
    <header>
        <h1>ALGAGES ADMIN DASHBOARD</h1>
    </header>

    <nav>
        <div class="container" style="margin:0 auto">
            <a href="dashboard.php">Inquiry Inbox</a>
            <a href="services.php">Service Manager</a>
            <?php if ($_SESSION['role'] === 'super_admin'): ?>
                <a href="users.php">User Management</a>
            <?php endif; ?>
            <a href="logout.php" style="float:right">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a>
        </div>
    </nav>

    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h2>Inquiry Inbox</h2>
            <?php if ($new_count > 0): ?>
                <div style="background: var(--prof-orange); color: white; padding: 0.5rem 1rem; border-radius: 20px; font-weight: bold; animation: pulse 2s infinite;">
                    <?php echo $new_count; ?> New Inquiry<?php echo $new_count > 1 ? 'ies' : ''; ?>
                </div>
            <?php endif; ?>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client Name</th>
                    <th>Email</th>
                    <th>WhatsApp</th>
                    <th>Est. Fee</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($submissions as $sub): ?>
                <tr>
                    <td><?php echo $sub['id']; ?></td>
                    <td><?php echo htmlspecialchars($sub['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($sub['email']); ?></td>
                    <td><?php echo htmlspecialchars($sub['whatsapp']); ?></td>
                    <td>$<?php echo number_format($sub['total_est_fee'], 2); ?></td>
                    <td>
                        <span class="status-badge status-<?php echo $sub['status']; ?>">
                            <?php echo $sub['status']; ?>
                        </span>
                    </td>
                    <td><?php echo date('Y-m-d H:i', strtotime($sub['created_at'])); ?></td>
                    <td>
                        <a href="view_submission.php?id=<?php echo $sub['id']; ?>" class="btn btn-orange" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
