<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'lms_database');

// Site Configuration
define('SITE_URL', 'http://localhost/lms/');
define('SITE_NAME', 'LMS - Learning Management System');
define('UPLOAD_PATH', 'assets/uploads/');
define('MAX_FILE_SIZE', 50 * 1024 * 1024); // 50MB

define('DEFAULT_USER_PASSWORD', 'SandiBaru2026'); // Password baru default untuk reset massal

// Session Configuration
define('SESSION_TIMEOUT', 3600); // 1 hour

// Create Database Connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        die("Database Connection Failed: " . $conn->connect_error);
    }

    // Set charset to UTF-8
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Helper Functions
function sanitize($input)
{
    global $conn;
    return $conn->real_escape_string(stripslashes(trim($input)));
}

function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

function get_user_role()
{
    return isset($_SESSION['role']) ? $_SESSION['role'] : null;
}

function redirect($location)
{
    header("Location: " . SITE_URL . $location);
    exit();
}

function alert($message, $type = 'info')
{
    $_SESSION['alert'] = ['message' => $message, 'type' => $type];
}

function get_alert()
{
    if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
        unset($_SESSION['alert']);
        return $alert;
    }
    return null;
}

function log_admin_action($action, $description = '')
{
    global $conn;

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
        return false;
    }

    // Ensure table exists before log insert
    $create_logs_table = "CREATE TABLE IF NOT EXISTS admin_logs (
        id INT PRIMARY KEY AUTO_INCREMENT,
        admin_id INT NOT NULL,
        action VARCHAR(255) NOT NULL,
        description TEXT,
        ip_address VARCHAR(45),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB";
    $conn->query($create_logs_table);

    $admin_id = (int)$_SESSION['user_id'];
    $ip_address = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown';

    $stmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, description, ip_address) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        error_log('log_admin_action prepare failed: ' . $conn->error);
        return false;
    }

    $stmt->bind_param('isss', $admin_id, $action, $description, $ip_address);
    $result = $stmt->execute();
    if (!$result) {
        error_log('log_admin_action execute failed: ' . $stmt->error);
    }
    $stmt->close();

    return $result;
}

function reset_all_passwords_to_default()
{
    global $conn;
    $default = DEFAULT_USER_PASSWORD;
    $hash = password_hash($default, PASSWORD_BCRYPT);
    $query = "UPDATE users SET password = '$hash'";
    return $conn->query($query);
}
