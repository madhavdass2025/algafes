<?php
session_start();
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// CSRF Validation
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("CSRF token validation failed.");
}

// Input Sanitization
$full_name = trim($_POST['full_name'] ?? '');
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$phone = trim($_POST['phone'] ?? '');
$whatsapp = trim($_POST['whatsapp'] ?? '');
$total_fee = filter_input(INPUT_POST, 'total_fee', FILTER_VALIDATE_FLOAT);
$selected_services = explode(',', $_POST['selected_services']);

if (!$full_name || !$email || !$whatsapp || !$total_fee) {
    die("Please fill in all required fields.");
}

try {
    $pdo->beginTransaction();

    // Insert Submission
    $stmt = $pdo->prepare("INSERT INTO client_submissions (full_name, email, phone, whatsapp, total_est_fee) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$full_name, $email, $phone, $whatsapp, $total_fee]);
    $submission_id = $pdo->lastInsertId();

    // Link Services
    $stmt = $pdo->prepare("INSERT INTO submission_services (submission_id, service_id) VALUES (?, ?)");
    foreach ($selected_services as $service_id) {
        $stmt->execute([$submission_id, trim($service_id)]);
    }

    // Handle File Uploads
    $upload_dir = __DIR__ . '/uploads/';
    $allowed_mimes = [
        'application/pdf',
        'application/zip',
        'application/x-zip-compressed',
        'image/vnd.dwg',
        'image/x-dwg',
        'application/acad',
        'application/x-acad',
        'application/autocad_dwg',
        'image/autocad_dwg',
        'drawing/dwg'
    ];

    if (!empty($_FILES['documents']['name'][0])) {
        foreach ($_FILES['documents']['name'] as $key => $name) {
            $tmp_name = $_FILES['documents']['tmp_name'][$key];
            $error = $_FILES['documents']['error'][$key];

            if ($error === UPLOAD_ERR_OK) {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($tmp_name);

                // Note: DWG detection can be tricky with finfo, might need to allow common extensions too if mime fails
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $allowed_exts = ['pdf', 'zip', 'dwg'];

                if (in_array($mime, $allowed_mimes) || in_array($ext, $allowed_exts)) {
                    $unique_name = bin2hex(random_bytes(8)) . '_' . $name;
                    $target_path = $upload_dir . $unique_name;

                    if (move_uploaded_file($tmp_name, $target_path)) {
                        $stmt = $pdo->prepare("INSERT INTO submission_files (submission_id, file_path, original_name) VALUES (?, ?, ?)");
                        $stmt->execute([$submission_id, $unique_name, $name]);
                    }
                }
            }
        }
    }

    $pdo->commit();

    // Success feedback
    $_SESSION['success_msg'] = "Thank you! Your request for $" . number_format($total_fee, 2) . " has been submitted. Our engineering team will contact you via WhatsApp/Email shortly.";
    header('Location: success.php');
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    die("An error occurred during submission: " . $e->getMessage());
}
?>
