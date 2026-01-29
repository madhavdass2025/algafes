<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
check_login();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: dashboard.php');
    exit;
}

// Fetch submission
$stmt = $pdo->prepare("SELECT * FROM client_submissions WHERE id = ?");
$stmt->execute([$id]);
$submission = $stmt->fetch();

if (!$submission) {
    die("Submission not found.");
}

// Update status if posted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    verify_csrf_token($_POST['csrf_token'] ?? '');
    $new_status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE client_submissions SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $id]);
    $submission['status'] = $new_status;
}

// Fetch services
$stmt = $pdo->prepare("SELECT s.title, s.base_price FROM services s JOIN submission_services ss ON s.id = ss.service_id WHERE ss.submission_id = ?");
$stmt->execute([$id]);
$services = $stmt->fetchAll();

// Fetch files
$stmt = $pdo->prepare("SELECT * FROM submission_files WHERE submission_id = ?");
$stmt->execute([$id]);
$files = $stmt->fetchAll();

$whatsapp_link = "https://wa.me/" . preg_replace('/[^0-9]/', '', $submission['whatsapp']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Submission #<?php echo $id; ?> - Algages</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        .card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        h3 { margin-top: 0; border-bottom: 2px solid var(--prof-orange); padding-bottom: 0.5rem; }
        .info-row { margin-bottom: 0.75rem; }
        .info-label { font-weight: bold; width: 150px; display: inline-block; }
    </style>
</head>
<body>
    <header>
        <h1>SUBMISSION DETAILS #<?php echo $id; ?></h1>
    </header>

    <div class="container">
        <a href="dashboard.php" style="display:inline-block; margin-bottom: 1rem;">← Back to Inbox</a>

        <div class="detail-grid">
            <div class="left-col">
                <div class="card">
                    <h3>Client Information</h3>
                    <div class="info-row"><span class="info-label">Full Name:</span> <?php echo htmlspecialchars($submission['full_name']); ?></div>
                    <div class="info-row"><span class="info-label">Email:</span> <?php echo htmlspecialchars($submission['email']); ?></div>
                    <div class="info-row"><span class="info-label">Phone:</span> <?php echo htmlspecialchars($submission['phone']); ?></div>
                    <div class="info-row"><span class="info-label">WhatsApp:</span> <?php echo htmlspecialchars($submission['whatsapp']); ?></div>
                    <br>
                    <a href="<?php echo $whatsapp_link; ?>" target="_blank" class="btn btn-orange">Contact on WhatsApp</a>
                </div>

                <div class="card">
                    <h3>Manage Status</h3>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <select name="status" class="form-group" style="width: 200px; padding: 0.5rem;">
                            <option value="new" <?php echo $submission['status'] == 'new' ? 'selected' : ''; ?>>New</option>
                            <option value="in-progress" <?php echo $submission['status'] == 'in-progress' ? 'selected' : ''; ?>>In Progress</option>
                            <option value="completed" <?php echo $submission['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                        </select>
                        <button type="submit" name="update_status" class="btn btn-orange" style="padding: 0.5rem 1rem;">Update</button>
                    </form>
                </div>
            </div>

            <div class="right-col">
                <div class="card">
                    <h3>Requested Services</h3>
                    <ul>
                        <?php foreach ($services as $s): ?>
                            <li><?php echo htmlspecialchars($s['title']); ?> - $<?php echo number_format($s['base_price'], 2); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <p><strong>Total Estimated Fee: $<?php echo number_format($submission['total_est_fee'], 2); ?></strong></p>
                </div>

                <div class="card">
                    <h3>Project Documents</h3>
                    <?php if (empty($files)): ?>
                        <p>No documents uploaded.</p>
                    <?php else: ?>
                        <ul>
                            <?php foreach ($files as $file): ?>
                                <li><a href="../uploads/<?php echo urlencode($file['file_path']); ?>" download="<?php echo htmlspecialchars($file['original_name']); ?>"><?php echo htmlspecialchars($file['original_name']); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
