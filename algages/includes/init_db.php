<?php
/**
 * DATABASE INITIALIZATION SCRIPT
 *
 * Note: This script is designed for quick setup using SQLite.
 * For production MySQL, use the provided includes/schema.sql in your MySQL manager.
 */
$db_file = __DIR__ . '/../database.sqlite';

try {
    // Create uploads directory if not exists
    $upload_dir = __DIR__ . '/../uploads';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $pdo = new PDO('sqlite:' . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create tables using SQLite-specific schema
    $sql = file_get_contents(__DIR__ . '/schema_sqlite.sql');
    $pdo->exec($sql);

    echo "Tables created successfully.\n";

    // Seed Categories
    $categories = [
        ['name' => 'ESTIMATION', 'sort_order' => 1],
        ['name' => 'DESIGN', 'sort_order' => 2],
        ['name' => 'SUBMITTALS', 'sort_order' => 3],
    ];

    $stmt = $pdo->prepare("INSERT OR IGNORE INTO categories (name, sort_order) VALUES (:name, :sort_order)");
    foreach ($categories as $cat) {
        $stmt->execute($cat);
    }
    echo "Categories seeded.\n";

    // Seed Services
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
        ['category_id' => 2, 'title' => 'Shop Drawings', 'description' => 'Detailed drawings that translate the design intent into a practical guide for site installation. They show how components should be assembled and coordinated with other trades.', 'base_price' => 1000.00],
        ['category_id' => 2, 'title' => 'Fabrication Drawings', 'description' => 'Highly specific drawings used by the workshop or factory for the manufacturing process. These include precise dimensions for cutting, welding, and drilling individual parts (e.g., steel members or ductwork).', 'base_price' => 800.00],
        ['category_id' => 2, 'title' => 'Structural Calculations', 'description' => 'The formal engineering analysis and mathematical data used to verify the integrity of a structure. This ensures the building can safely support all intended loads (dead, live, wind, and seismic) according to local codes.', 'base_price' => 1200.00],
        ['category_id' => 2, 'title' => 'As-Built Drawings', 'description' => 'The final set of drawings submitted after construction is complete. They reflect the actual "as-constructed" state of the project, documenting any deviations or changes made from the original design.', 'base_price' => 600.00],

        // SUBMITTALS
        ['category_id' => 3, 'title' => 'Prequalification Document (PQD)', 'description' => 'A formal package submitted to prove a company\'s capability, experience, and financial stability before being invited to bid.', 'base_price' => 300.00],
        ['category_id' => 3, 'title' => 'Method Statement', 'description' => 'A step-by-step guide detailing how a specific task will be executed safely and efficiently on-site.', 'base_price' => 250.00],
        ['category_id' => 3, 'title' => 'Material Submittals', 'description' => 'A formal package (including data sheets, samples, and test reports) provided to the consultant for approval of the materials to be used.', 'base_price' => 200.00],
        ['category_id' => 3, 'title' => 'Inspection and Test Plan (ITP)', 'description' => 'A structured document that outlines the quality control points, inspection types, and "witness" or "hold" points throughout the construction process.', 'base_price' => 350.00],
        ['category_id' => 3, 'title' => 'Risk Assessment (RA)', 'description' => 'A systematic process of identifying potential hazards associated with a task and implementing measures to reduce or eliminate them.', 'base_price' => 200.00],
    ];

    $stmt = $pdo->prepare("INSERT OR IGNORE INTO services (category_id, title, description, base_price) VALUES (:category_id, :title, :description, :base_price)");
    foreach ($services as $service) {
        $stmt->execute($service);
    }
    echo "Services seeded.\n";

    // Seed Admin User
    $admin_user = [
        'username' => 'admin',
        'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
        'role' => 'super_admin'
    ];
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO users (username, password_hash, role) VALUES (:username, :password_hash, :role)");
    $stmt->execute($admin_user);
    echo "Admin user seeded (admin/admin123).\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
