<?php
session_start();
$message = isset($_SESSION['success_msg']) ? $_SESSION['success_msg'] : "Your request has been submitted.";
unset($_SESSION['success_msg']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submission Successful - Algages</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .success-container {
            text-align: center;
            padding: 5rem 1rem;
        }
        .success-icon {
            font-size: 5rem;
            color: #27ae60;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <header>
        <h1>ALGAGES FACADE ENGINEERING</h1>
    </header>
    <div class="container success-container">
        <div class="success-icon">✓</div>
        <h2>Success!</h2>
        <p><?php echo htmlspecialchars($message); ?></p>
        <br>
        <a href="index.php" class="btn btn-orange">Back to Services</a>
    </div>
</body>
</html>
