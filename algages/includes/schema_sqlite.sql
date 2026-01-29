CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    sort_order INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS services (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    category_id INTEGER NOT NULL,
    title TEXT NOT NULL,
    description TEXT,
    base_price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    role TEXT CHECK(role IN ('super_admin', 'editor', 'viewer')) NOT NULL
);

CREATE TABLE IF NOT EXISTS client_submissions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    full_name TEXT NOT NULL,
    email TEXT NOT NULL,
    phone TEXT,
    whatsapp TEXT,
    total_est_fee DECIMAL(10, 2) NOT NULL,
    status TEXT CHECK(status IN ('new', 'in-progress', 'completed')) DEFAULT 'new',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS submission_files (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    submission_id INTEGER NOT NULL,
    file_path TEXT NOT NULL,
    original_name TEXT NOT NULL,
    FOREIGN KEY (submission_id) REFERENCES client_submissions(id)
);

CREATE TABLE IF NOT EXISTS submission_services (
    submission_id INTEGER NOT NULL,
    service_id INTEGER NOT NULL,
    PRIMARY KEY (submission_id, service_id),
    FOREIGN KEY (submission_id) REFERENCES client_submissions(id),
    FOREIGN KEY (service_id) REFERENCES services(id)
);
