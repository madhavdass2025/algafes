<?php
/**
 * MYSQL DATABASE INITIALIZATION SCRIPT (MySQLi Version)
 */

require_once __DIR__ . '/db.php';

// 1. Create Tables
$sql = file_get_contents(__DIR__ . '/schema.sql');
$queries = explode(';', $sql);
foreach ($queries as $query) {
    $query = trim($query);
    if ($query) {
        if (!mysqli_query($conn, $query)) {
            echo "Error creating table: " . mysqli_error($conn) . "\n";
        }
    }
}
echo "Tables created successfully.\n";

// 2. Seed Categories
$categories = [
    ['name' => 'ESTIMATION', 'sort_order' => 1],
    ['name' => 'DESIGN', 'sort_order' => 2],
    ['name' => 'SUBMITTALS', 'sort_order' => 3],
];

foreach ($categories as $cat) {
    $name = mysqli_real_escape_string($conn, $cat['name']);
    $sort = (int)$cat['sort_order'];
    $sql = "INSERT INTO categories (name, sort_order) VALUES ('$name', $sort) ON DUPLICATE KEY UPDATE name=name";
    mysqli_query($conn, $sql);
}
echo "Categories seeded.\n";

// 3. Seed Services
$services = [
    ['category_id' => 1, 'title' => 'Quantity Take-off (QS) from IFC Drawings', 'description' => 'Extracting accurate measurements and quantities from "Issued for Construction" (IFC) drawings.', 'base_price' => 500.00],
    ['category_id' => 1, 'title' => 'BOQ Preparation', 'description' => 'Developing a comprehensive Bill of Quantities based on the measured data.', 'base_price' => 300.00],
    ['category_id' => 1, 'title' => 'Compliance Statement', 'description' => 'Drafting a statement to confirm that all proposed materials and methods meet the project specifications.', 'base_price' => 200.00],
    ['category_id' => 1, 'title' => 'Material Take-off (MTO)', 'description' => 'Generating a detailed list of all physical materials required for the project.', 'base_price' => 250.00],
    ['category_id' => 1, 'title' => 'Supplier Inquiries & Price Negotiation', 'description' => 'Sending out inquiries to approved vendors and negotiating competitive pricing.', 'base_price' => 400.00],
    ['category_id' => 1, 'title' => 'Material Costing & Analysis', 'description' => 'Finalizing the total project cost based on the detailed material list and supplier quotes.', 'base_price' => 350.00],
    ['category_id' => 1, 'title' => 'Offer Letter / Quotation Preparation', 'description' => 'Preparing the final formal bid or commercial proposal to be submitted to the client.', 'base_price' => 150.00],
    ['category_id' => 2, 'title' => 'Shop Drawings', 'description' => 'Detailed drawings that translate the design intent into a practical guide for site installation.', 'base_price' => 1000.00],
    ['category_id' => 2, 'title' => 'Fabrication Drawings', 'description' => 'Highly specific drawings used by the workshop or factory for the manufacturing process.', 'base_price' => 800.00],
    ['category_id' => 2, 'title' => 'Structural Calculations', 'description' => 'The formal engineering analysis and mathematical data used to verify the integrity of a structure.', 'base_price' => 1200.00],
    ['category_id' => 2, 'title' => 'As-Built Drawings', 'description' => 'The final set of drawings submitted after construction is complete.', 'base_price' => 600.00],
    ['category_id' => 3, 'title' => 'Prequalification Document (PQD)', 'description' => 'A formal package submitted to prove a company\'s capability and experience.', 'base_price' => 300.00],
    ['category_id' => 3, 'title' => 'Method Statement', 'description' => 'A step-by-step guide detailing how a specific task will be executed safely.', 'base_price' => 250.00],
    ['category_id' => 3, 'title' => 'Material Submittals', 'description' => 'A formal package (including data sheets and samples) for consultant approval.', 'base_price' => 200.00],
    ['category_id' => 3, 'title' => 'Inspection and Test Plan (ITP)', 'description' => 'A structured document that outlines the quality control points.', 'base_price' => 350.00],
    ['category_id' => 3, 'title' => 'Risk Assessment (RA)', 'description' => 'Identifying potential hazards and implementing measures to reduce them.', 'base_price' => 200.00],
];

foreach ($services as $s) {
    $cid = (int)$s['category_id'];
    $title = mysqli_real_escape_string($conn, $s['title']);
    $desc = mysqli_real_escape_string($conn, $s['description']);
    $price = (float)$s['base_price'];
    $sql = "INSERT INTO services (category_id, title, description, base_price) VALUES ($cid, '$title', '$desc', $price) ON DUPLICATE KEY UPDATE title=title";
    mysqli_query($conn, $sql);
}
echo "Services seeded.\n";

// 4. Seed Admin User
$username = 'admin';
$hash = password_hash('admin123', PASSWORD_DEFAULT);
$role = 'super_admin';
$sql = "INSERT INTO users (username, password_hash, role) VALUES ('$username', '$hash', '$role') ON DUPLICATE KEY UPDATE username=username";
mysqli_query($conn, $sql);
echo "Admin user seeded (admin/admin123).\n";

// 5. Ensure Uploads directory
$upload_dir = __DIR__ . '/../uploads';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
    echo "Uploads directory created.\n";
}
?>
