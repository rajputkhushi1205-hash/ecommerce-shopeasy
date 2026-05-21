<?php
// ── Database Configuration ─────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ecommerce_db');

// ── Razorpay Configuration ─────────────────────────────────
define('RAZORPAY_KEY_ID', 'rzp_test_Sqt3jJYHY5tTQO');         // Replace with your key
define('RAZORPAY_KEY_SECRET', 'CjDpfTsYn4a66cPIPII6bbw6');           // Replace with your secret

// ── Site Configuration ─────────────────────────────────────
define('SITE_NAME', 'ShopEasy');
define('SITE_URL', 'http://localhost/ecommerce');
define('CURRENCY', '₹');

// ── Database Connection ────────────────────────────────────
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");

// ── Session Start ──────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Helper Functions ───────────────────────────────────────

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Redirect function
function redirect($url) {
    header("Location: " . SITE_URL . "/" . $url);
    exit();
}

// Sanitize input
function clean($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}

// Get cart count
function getCartCount() {
    global $conn;
    if (!isLoggedIn()) return 0;
    $user_id = $_SESSION['user_id'];
    $result = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart WHERE user_id = $user_id");
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

// Format price
function formatPrice($price) {
    return CURRENCY . number_format($price, 2);
}

// Calculate discount percentage
function discountPercent($old, $new) {
    if ($old > 0) {
        return round((($old - $new) / $old) * 100);
    }
    return 0;
}
?>
