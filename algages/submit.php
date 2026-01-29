<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// CSRF Validation
verify_csrf_token($_POST['csrf_token'] ?? '');

// Input Sanitization & Escaping
$full_name = mysqli_real_escape_string($conn, trim($_POST['full_name'] ?? ''));
$email = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
$phone = mysqli_real_escape_string($conn, trim($_POST['phone'] ?? ''));
$whatsapp = mysqli_real_escape_string($conn, trim($_POST['whatsapp'] ?? ''));
$total_fee = (float)($_POST['total_fee'] ?? 0);
$selected_services = explode(',', $_POST['selected_services']);

if (!$full_name || !$email || !$whatsapp || !$total_fee) {
    die("Please fill in all required fields.");
}

mysqli_begin_transaction($conn);

try {
    // Insert Submission
    $sql = "INSERT INTO client_submissions (full_name, email, phone, whatsapp, total_est_fee)
            VALUES ('$full_name', '$email', '$phone', '$whatsapp', $total_fee)";
    mysqli_query($conn, $sql);
    $submission_id = mysqli_insert_id($conn);

    // Link Services
    foreach ($selected_services as $service_id) {
        $sid = (int)$service_id;
        $sql_service = "INSERT INTO submission_services (submission_id, service_id) VALUES ($submission_id, $sid)";
        mysqli_query($conn, $sql_service);
    }

    // Handle File Uploads
    $upload_dir = __DIR__ . '/uploads/';
    $allowed_mimes = [
        'application/pdf', 'application/zip', 'application/x-zip-compressed',
        'image/vnd.dwg', 'image/x-dwg', 'application/acad', 'application/x-acad',
        'application/autocad_dwg', 'image/autocad_dwg', 'drawing/dwg'
    ];

    if (!empty($_FILES['documents']['name'][0])) {
        foreach ($_FILES['documents']['name'] as $key => $name) {
            $tmp_name = $_FILES['documents']['tmp_name'][$key];
            $error = $_FILES['documents']['error'][$key];

            if ($error === UPLOAD_ERR_OK) {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($tmp_name);
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $allowed_exts = ['pdf', 'zip', 'dwg'];

                if (in_array($mime, $allowed_mimes) || in_array($ext, $allowed_exts)) {
                    $unique_name = bin2hex(random_bytes(8)) . '_' . $name;
                    $target_path = $upload_dir . $unique_name;

                    if (move_uploaded_file($tmp_name, $target_path)) {
                        $esc_unique = mysqli_real_escape_string($conn, $unique_name);
                        $esc_orig = mysqli_real_escape_string($conn, $name);
                        $sql_file = "INSERT INTO submission_files (submission_id, file_path, original_name)
                                     VALUES ($submission_id, '$esc_unique', '$esc_orig')";
                        mysqli_query($conn, $sql_file);
                    }
                }
            }
        }
    }

    mysqli_commit($conn);

    $_SESSION['success_msg'] = "Thank you! Your request for $" . number_format($total_fee, 2) . " has been submitted. Our engineering team will contact you via WhatsApp/Email shortly.";
    header('Location: success.php');
    exit;

} catch (Exception $e) {
    mysqli_rollback($conn);
    die("An error occurred during submission: " . $e->getMessage());
}
?>
