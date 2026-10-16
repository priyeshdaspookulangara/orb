<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'orbita_db');
define('DB_USER', 'root');
define('DB_PASS', '');

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            try {
                $tempPdo = new PDO("mysql:host=" . DB_HOST . ";charset=utf8mb4", DB_USER, DB_PASS);
                $tempPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "`");
                $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $ex) {
                die("Database connection failed: " . $ex->getMessage());
            }
        }
    }
    return $pdo;
}

function initDB() {
    $pdo = getDB();

    $queries = [
        "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id VARCHAR(20) UNIQUE NOT NULL,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            phone VARCHAR(20) NOT NULL,
            password VARCHAR(255) NOT NULL,
            sponsor_id VARCHAR(20) DEFAULT NULL,
            parent_id VARCHAR(20) DEFAULT NULL,
            level INT NOT NULL DEFAULT 0,
            children_count INT NOT NULL DEFAULT 0,
            wallet_balance DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
            is_admin TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_level_children (level, children_count),
            INDEX idx_sponsor (sponsor_id),
            INDEX idx_parent (parent_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_number VARCHAR(30) UNIQUE NOT NULL,
            user_id VARCHAR(20) NOT NULL,
            flavor VARCHAR(50) NOT NULL,
            package_amount DECIMAL(10, 2) NOT NULL DEFAULT 6000.00,
            shipping_address TEXT NOT NULL,
            fulfillment_status ENUM('Processing', 'Dispatched', 'Delivered') DEFAULT 'Processing',
            tracking_number VARCHAR(100) DEFAULT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_order_user (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "CREATE TABLE IF NOT EXISTS wallet_transactions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id VARCHAR(20) NOT NULL,
            from_user_id VARCHAR(20) NOT NULL,
            type ENUM('DIRECT_REFERRAL', 'MATRIX_COMMISSION') NOT NULL,
            amount DECIMAL(10, 2) NOT NULL,
            description VARCHAR(255) NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_tx_user (user_id),
            INDEX idx_tx_from (from_user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
    ];

    foreach ($queries as $sql) {
        $pdo->exec($sql);
    }

    // Seed Root Node ORB00000 if not existing
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE user_id = 'ORB00000'");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $rootPass = password_hash('Admin@123', PASSWORD_DEFAULT);
        $stmtInsert = $pdo->prepare("INSERT INTO users (user_id, name, email, phone, password, sponsor_id, parent_id, level, children_count, wallet_balance, is_admin) VALUES ('ORB00000', 'Orbita Root Admin', 'admin@orbitaprotein.com', '+91 98765 43210', ?, NULL, NULL, 0, 0, 0.00, 1)");
        $stmtInsert->execute([$rootPass]);
    }
}

// Automatically initialize schema
initDB();

// Helper Functions
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getCurrentUser() {
    if (!isLoggedIn()) return null;
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

function requireAdmin() {
    $user = getCurrentUser();
    if (!$user || !$user['is_admin']) {
        header("Location: dashboard.php");
        exit;
    }
}

function generateNextUserId() {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT user_id FROM users WHERE user_id LIKE 'ORB%' ORDER BY LENGTH(user_id) DESC, user_id DESC LIMIT 1");
    $lastUserId = $stmt->fetchColumn();
    if ($lastUserId) {
        $num = (int) substr($lastUserId, 3);
        $nextNum = $num + 1;
    } else {
        $nextNum = 0;
    }
    return sprintf("ORB%05d", $nextNum);
}

function formatINR($amount) {
    return '₹' . number_format($amount, 2);
}
