<?php
/**
 * MYSQL DATABASE INITIALIZATION SCRIPT
 *
 * This script will:
 * 1. Create the necessary tables using schema.sql
 * 2. Seed initial categories, services, and a default admin user.
 *
 * Usage:
 * Update your MySQL credentials in includes/db.php first.
 * Then run: php init_db.php
 */

require_once __DIR__ . '/db.php';

try {
    // 1. Create Tables
    $sql = file_get_contents(__DIR__ . '/schema.sql');
    // For MySQL, we might need to split the queries if the driver doesn't support multiple in one exec
    $queries = explode(';', $sql);
    foreach ($queries as $query) {
        $query = trim($query);
        if ($query) {
            $pdo->exec($query);
        }
    }
    echo "Tables created successfully.\n";

    // 2. Seed Categories
    $categories = [
        ['name' => 'ESTIMATION', 'sort_order' => 1],
        ['name' => 'DESIGN', 'sort_order' => 2],
        ['name' => 'SUBMITTALS', 'sort_order' => 3],
    ];

    $stmt = $pdo->prepare("INSERT INTO categories (name, sort_order) VALUES (:name, :sort_order) ON DUPLICATE KEY UPDATE name=name");
    foreach ($categories as $cat) {
        $stmt->execute($cat);
    }
    echo "Categories seeded.\n";

    // 3. Seed Services
    $services = [
        // ESTIMATION
        ['category_id' => 1, 'title' => 'Quantity Take-off (QS) from IFC Drawings', 'description' => 'Extracting accurate measurements and quantities from "Issued for Construction" (IFC) drawings.', 'base_price' => 500.00],
        ['category_id' => 1, 'title' => 'BOQ Preparation', 'description' => 'Developing a comprehensive Bill of Quantities based on the measured data.', 'base_price' => 300.00],
        ['category_id' => 1, 'title' => 'Compliance Statement', 'description' => 'Drafting a statement to confirm that all proposed materials and methods meet the project specifications.', 'base_price' => 200.00],
        ['category_id' => 1, 'title' => 'Material Take-off (MTO)', 'description' => 'Generating a detailed list of all physical materials required for the project.', 'base_price' => 250.00],
        ['category_id' => 1, 'title' => 'Supplier Inquiries & Price Negotiation', 'description' => 'Sending out inquiries to approved vendors (per the Project Vendor List) and negotiating competitive pricing.', 'base_price' => 400.00],
        ['category_id' => 1, 'title' => 'Material Costing & Analysis', 'description' => 'Finalizing the total project cost based on the detailed material list and supplier quotes.', 'base_price' => 350.00],
        ['category_id' => 1, 'title' => 'Offer Letter / Quotation Preparation', 'description' => 'Preparing the final formal bid or commercial proposal to be submitted to the client.', 'base_price' => 150.00],

        // DESIGN
        ['category_id' => 2, 'title' => 'Shop Drawings', 'description' => 'Detailed drawings that translate the design intent into a practical guide for site installation.', 'base_price' => 1000.00],
        ['category_id' => 2, 'title' => 'Fabrication Drawings', 'description' => 'Highly specific drawings used by the workshop or factory for the manufacturing process.', 'base_price' => 800.00],
        ['category_id' => 2, 'title' => 'Structural Calculations', 'description' => 'The formal engineering analysis and mathematical data used to verify the integrity of a structure.', 'base_price' => 1200.00],
        ['category_id' => 2, 'title' => 'As-Built Drawings', 'description' => 'The final set of drawings submitted after construction is complete.', 'base_price' => 600.00],

        // SUBMITTALS
        ['category_id' => 3, 'title' => 'Prequalification Document (PQD)', 'description' => 'A formal package submitted to prove a company\'s capability and experience.', 'base_price' => 300.00],
        ['category_id' => 3, 'title' => 'Method Statement', 'description' => 'A step-by-step guide detailing how a specific task will be executed safely.', 'base_price' => 250.00],
        ['category_id' => 3, 'title' => 'Material Submittals', 'description' => 'A formal package (including data sheets and samples) for consultant approval.', 'base_price' => 200.00],
        ['category_id' => 3, 'title' => 'Inspection and Test Plan (ITP)', 'description' => 'A structured document that outlines the quality control points.', 'base_price' => 350.00],
        ['category_id' => 3, 'title' => 'Risk Assessment (RA)', 'description' => 'Identifying potential hazards and implementing measures to reduce them.', 'base_price' => 200.00],
    ];

    $stmt = $pdo->prepare("INSERT INTO services (category_id, title, description, base_price) VALUES (:category_id, :title, :description, :base_price) ON DUPLICATE KEY UPDATE title=title");
    foreach ($services as $service) {
        $stmt->execute($service);
    }
    echo "Services seeded.\n";

    // 4. Seed Admin User
    $admin_user = [
        'username' => 'admin',
        'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
        'role' => 'super_admin'
    ];
    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES (:username, :password_hash, :role) ON DUPLICATE KEY UPDATE username=username");
    $stmt->execute($admin_user);
    echo "Admin user seeded (admin/admin123).\n";

    // 5. Ensure Uploads directory
    $upload_dir = __DIR__ . '/../uploads';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
        echo "Uploads directory created.\n";
    }

} catch (PDOException $e) {
    die("Error during initialization: " . $e->getMessage() . "\n");
}
?>
