-- MySQL / PDO Compatible Schema
-- For MySQL, use INT AUTO_INCREMENT instead of INTEGER PRIMARY KEY AUTOINCREMENT

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    base_price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'editor', 'viewer') NOT NULL
);

CREATE TABLE IF NOT EXISTS client_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    whatsapp VARCHAR(50),
    total_est_fee DECIMAL(10, 2) NOT NULL,
    status ENUM('new', 'in-progress', 'completed') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS submission_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    submission_id INT NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    FOREIGN KEY (submission_id) REFERENCES client_submissions(id)
);

CREATE TABLE IF NOT EXISTS submission_services (
    submission_id INT NOT NULL,
    service_id INT NOT NULL,
    PRIMARY KEY (submission_id, service_id),
    FOREIGN KEY (submission_id) REFERENCES client_submissions(id),
    FOREIGN KEY (service_id) REFERENCES services(id)
);
