<?php
require_once 'config.php';
if (!isLoggedIn() || $_SERVER['REQUEST_METHOD'] !== 'POST') redirect('index.php');

$user_id              = $_SESSION['user_id'];
$razorpay_payment_id  = clean($_POST['razorpay_payment_id']);
$razorpay_order_id    = clean($_POST['razorpay_order_id']);
$address              = clean($_POST['address']);
$amount               = (float)$_POST['amount'];

// ── Save Order ─────────────────────────────────────────────
mysqli_query($conn, "INSERT INTO orders (user_id, total_amount, status, razorpay_order_id, razorpay_payment_id, address)
    VALUES ($user_id, $amount, 'paid', '$razorpay_order_id', '$razorpay_payment_id', '$address')");

$order_id = mysqli_insert_id($conn);

// ── Save Order Items ───────────────────────────────────────
$cart_items = mysqli_query($conn, "SELECT c.quantity, p.id as product_id, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = $user_id");
while ($item = mysqli_fetch_assoc($cart_items)) {
    mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ($order_id, {$item['product_id']}, {$item['quantity']}, {$item['price']})");
}

// ── Clear Cart ─────────────────────────────────────────────
mysqli_query($conn, "DELETE FROM cart WHERE user_id = $user_id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Successful - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<nav class="navbar">
    <div class="nav-container">
        <a href="index.php" class="logo"><i class="fas fa-shopping-bag"></i> <?= SITE_NAME ?></a>
    </div>
</nav>

<div class="container success-container">
    <div class="success-icon">
        <i class="fas fa-check-circle"></i>
    </div>
    <h2>Payment Successful! 🎉</h2>
    <p>Thank you for your order! Your payment has been processed successfully.</p>

    <div style="background:white;border-radius:10px;padding:24px;max-width:400px;margin:0 auto 30px;box-shadow:0 2px 15px rgba(0,0,0,0.1);text-align:left;">
        <h4 style="margin-bottom:16px;color:#1a237e;">Order Details</h4>
        <div style="display:flex;justify-content:space-between;margin-bottom:10px;">
            <span style="color:#888;">Order ID:</span>
            <strong>#<?= $order_id ?></strong>
        </div>
        <div style="display:flex;justify-content:space-between;margin-bottom:10px;">
            <span style="color:#888;">Payment ID:</span>
            <strong style="font-size:12px;"><?= $razorpay_payment_id ?></strong>
        </div>
        <div style="display:flex;justify-content:space-between;margin-bottom:10px;">
            <span style="color:#888;">Amount Paid:</span>
            <strong style="color:#26a541;"><?= formatPrice($amount) ?></strong>
        </div>
        <div style="display:flex;justify-content:space-between;">
            <span style="color:#888;">Status:</span>
            <span style="background:#e8f5e9;color:#2e7d32;padding:4px 12px;border-radius:50px;font-size:13px;font-weight:700;">✅ Paid</span>
        </div>
    </div>

    <div style="display:flex;gap:16px;justify-content:center;">
        <a href="orders.php" class="btn-continue" style="background:#2874f0;">
            <i class="fas fa-box"></i> View My Orders
        </a>
        <a href="index.php" class="btn-continue">
            <i class="fas fa-shopping-bag"></i> Continue Shopping
        </a>
    </div>
</div>
</body>
</html>
