<?php
// Secure session configuration - Check lagaya taake active session warning na aaye
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.use_only_cookies', 1);
    ini_set('session.gc_maxlifetime', 3600); // 1 hour timeout
    session_start();
}

// Session timeout check (1 hour)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 3600)) {
    session_unset();
    session_destroy();
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}
$_SESSION['last_activity'] = time();

// Database configuration
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'iqbalkilani_user');
define('DB_PASS', 'Batam3xpgl.,');
define('DB_NAME', 'iqbalkilani_db');
// Try to connect to the database
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Auto-create Fatwas table (Adding this to match your flow)
    $pdo->exec("CREATE TABLE IF NOT EXISTS `fatwas` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `user_id` int(11) DEFAULT NULL,
      `reference_no` varchar(50) NOT NULL,
      `user_name` varchar(100) NOT NULL,
      `user_email` varchar(100) NOT NULL,
      `category` varchar(100) NOT NULL,
      `question_text` text NOT NULL,
      `answer_text` text DEFAULT NULL,
      `status` varchar(20) DEFAULT 'Pending',
      `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // Auto-create Audios table for the new voice section
    $pdo->exec("CREATE TABLE IF NOT EXISTS `audios` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `title` varchar(255) NOT NULL,
      `description` text,
      `audio_url` varchar(255) NOT NULL,
      `duration` varchar(20) DEFAULT NULL,
      `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    
    // Auto-create Users table for public accounts
    $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(100) NOT NULL,
      `email` varchar(100) NOT NULL,
      `password_hash` varchar(255) NOT NULL,
      `status` varchar(20) DEFAULT 'active',
      `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // Add status column if missing (for existing tables)
    try { $pdo->exec("ALTER TABLE users ADD COLUMN `status` varchar(20) DEFAULT 'active' AFTER `password_hash`"); } catch(PDOException $e) {}

    // Auto-create Admin Users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `admin_users` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `username` varchar(50) NOT NULL,
      `password_hash` varchar(255) NOT NULL,
      `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `username` (`username`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // Seed default admin account if table is empty
    $adminCount = $pdo->query("SELECT COUNT(*) FROM admin_users")->fetchColumn();
    if ($adminCount == 0) {
        $defaultHash = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO admin_users (username, password_hash) VALUES (?, ?)")->execute(['admin', $defaultHash]);
    }

    // Auto-create Orders table for book orders
    $pdo->exec("CREATE TABLE IF NOT EXISTS `orders` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `book_id` int(11) NOT NULL,
      `customer_name` varchar(100) NOT NULL,
      `customer_email` varchar(100) DEFAULT NULL,
      `customer_phone` varchar(30) DEFAULT NULL,
      `shipping_address` text NOT NULL,
      `status` varchar(20) DEFAULT 'Pending',
      `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // Auto-create Order History table for tracking status changes
    $pdo->exec("CREATE TABLE IF NOT EXISTS `order_history` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `order_id` int(11) NOT NULL,
      `old_status` varchar(20) DEFAULT NULL,
      `new_status` varchar(20) NOT NULL,
      `changed_by` varchar(50) DEFAULT NULL,
      `changed_at` timestamp DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // Auto-create Enrollments table for course enrollments
    $pdo->exec("CREATE TABLE IF NOT EXISTS `enrollments` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `course_id` int(11) NOT NULL,
      `user_name` varchar(100) NOT NULL,
      `user_email` varchar(100) NOT NULL,
      `user_phone` varchar(30) DEFAULT NULL,
      `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

} catch(PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}

// Security function to sanitize input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Secure file upload validation
define('MAX_UPLOAD_SIZE', 20 * 1024 * 1024); // 20MB

function validate_upload($file, $allowed_extensions, $allowed_mimes, $max_size = MAX_UPLOAD_SIZE) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return "Upload failed with error code: " . $file['error'];
    }
    if ($file['size'] > $max_size) {
        return "File too large. Maximum size: " . ($max_size / 1024 / 1024) . "MB";
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_extensions)) {
        return "Invalid file type. Allowed: " . implode(', ', $allowed_extensions);
    }
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if (!in_array($mime, $allowed_mimes)) {
        return "Invalid file content. Detected: $mime";
    }
    return true;
}

// Rate limiting (session-based)
function rate_limit($key, $max_requests = 5, $window = 300) {
    $now = time();
    if (!isset($_SESSION['rate_limit'][$key])) {
        $_SESSION['rate_limit'][$key] = [];
    }
    // Remove expired entries
    $_SESSION['rate_limit'][$key] = array_filter($_SESSION['rate_limit'][$key], function($t) use ($now, $window) {
        return ($now - $t) < $window;
    });
    if (count($_SESSION['rate_limit'][$key]) >= $max_requests) {
        return false; // Rate limited
    }
    $_SESSION['rate_limit'][$key][] = $now;
    return true;
}

// CSRF token generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Simple translation array logic for demonstration
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'en'; // default english
}

$lang = $_SESSION['lang'];

// Site URL
define('SITE_URL', 'https://iqbalkilani.store/');
?>
