<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Fetch categories
$sql = "SELECT * FROM categories ORDER BY sort_order";
$result = mysqli_query($conn, $sql);
$categories = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Fetch services
$sql = "SELECT * FROM services";
$result = mysqli_query($conn, $sql);
$all_services = mysqli_fetch_all($result, MYSQLI_ASSOC);

$services_by_cat = [];
foreach ($all_services as $service) {
    $services_by_cat[$service['category_id']][] = $service;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Algages Facade Engineering - Service Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <h1>ALGAGES FACADE ENGINEERING</h1>
    <p>Professional Engineering Solutions for Building Envelopes</p>
</header>

<div class="container">
    <div class="grid">
        <?php foreach ($categories as $cat): ?>
            <div class="category-column">
                <div class="category-header"><?php echo htmlspecialchars($cat['name']); ?></div>
                <div class="services-list">
                    <?php if (isset($services_by_cat[$cat['id']])): ?>
                        <?php foreach ($services_by_cat[$cat['id']] as $service): ?>
                            <div class="service-item">
                                <div class="service-info">
                                    <span class="service-title"><?php echo htmlspecialchars($service['title']); ?></span>
                                    <span class="service-description"><?php echo htmlspecialchars($service['description']); ?></span>
                                </div>
                                <input type="checkbox" class="service-checkbox"
                                       value="<?php echo $service['id']; ?>"
                                       data-price="<?php echo $service['base_price']; ?>">
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="sticky-footer">
    <div>
        <strong>Estimated Total: $<span id="running-total">0.00</span></strong>
    </div>
    <button id="proceed-btn" class="btn btn-orange" disabled>Generate Fees</button>
</div>

<!-- Submission Modal -->
<div id="submission-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Project Submission</h2>

        <div class="summary-box">
            <h3>Selected Services</h3>
            <ul id="summary-services"></ul>
            <p><strong>Total Estimated Fee: $<span id="modal-total">0.00</span></strong></p>
        </div>

        <form id="submission-form" action="submit.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="selected_services" id="selected-services-input">
            <input type="hidden" name="total_fee" id="total-fee-input">

            <div class="form-group">
                <label for="full_name">Full Name *</label>
                <input type="text" id="full_name" name="full_name" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone">
            </div>

            <div class="form-group">
                <label for="whatsapp">WhatsApp Number *</label>
                <input type="tel" id="whatsapp" name="whatsapp" required>
            </div>

            <div class="form-group">
                <label>Project Documents (IFC/Shop Drawings - PDF, DWG, ZIP)</label>
                <div class="upload-area" onclick="document.getElementById('file-upload').click()">
                    Drag & Drop or Click to Upload
                    <input type="file" id="file-upload" name="documents[]" multiple style="display:none"
                           onchange="document.getElementById('file-list').textContent = this.files.length + ' file(s) selected'">
                    <div id="file-list" style="margin-top:10px; font-size: 0.9rem; color: var(--prof-orange);"></div>
                </div>
            </div>

            <button type="submit" class="btn btn-orange" style="width:100%">Submit Request</button>
        </form>
    </div>
</div>

<script src="assets/js/calculator.js"></script>
</body>
</html>
