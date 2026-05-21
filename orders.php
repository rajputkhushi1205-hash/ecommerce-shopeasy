<?php
require_once 'config.php';
if (!isLoggedIn()) redirect('login.php');
$user_id = $_SESSION['user_id'];
$orders = mysqli_query($conn, "SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<nav class="navbar">
    <div class="nav-container">
        <a href="index.php" class="logo"><i class="fas fa-shopping-bag"></i> <?= SITE_NAME ?></a>
        <div class="nav-icons">
            <a href="index.php"><i class="fas fa-home"></i> Home</a>
            <a href="cart.php" class="cart-icon"><i class="fas fa-shopping-cart"></i><span class="cart-badge"><?= getCartCount() ?></span></a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </div>
</nav>

<div class="orders-container container">
    <h2 style="margin-bottom:24px;"><i class="fas fa-box"></i> My Orders</h2>

    <?php if(mysqli_num_rows($orders) === 0): ?>
        <div style="text-align:center;padding:60px;background:white;border-radius:10px;">
            <i class="fas fa-box-open" style="font-size:80px;color:#ddd;display:block;margin-bottom:20px;"></i>
            <h3>No orders yet!</h3>
            <a href="index.php" style="color:#2874f0;font-weight:600;">Start Shopping →</a>
        </div>
    <?php else: ?>
        <?php while($order = mysqli_fetch_assoc($orders)):
            $items = mysqli_query($conn, "SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = {$order['id']}");
            $statusClass = 'status-' . $order['status'];
        ?>
        <div class="order-card">
            <div class="order-header">
                <div>
                    <strong>Order #<?= $order['id'] ?></strong>
                    <span style="margin-left:16px;font-size:13px;opacity:0.8;"><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></span>
                </div>
                <div style="display:flex;align-items:center;gap:16px;">
                    <strong><?= formatPrice($order['total_amount']) ?></strong>
                    <span class="status-badge <?= $statusClass ?>"><?= strtoupper($order['status']) ?></span>
                </div>
            </div>
            <div class="order-body">
                <?php while($item = mysqli_fetch_assoc($items)): ?>
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f5f5f5;">
                    <span><?= $item['name'] ?> × <?= $item['quantity'] ?></span>
                    <strong><?= formatPrice($item['price'] * $item['quantity']) ?></strong>
                </div>
                <?php endwhile; ?>
                <div style="margin-top:12px;font-size:13px;color:#888;">
                    <i class="fas fa-map-marker-alt"></i> <?= $order['address'] ?>
                </div>
                <?php if($order['razorpay_payment_id']): ?>
                <div style="margin-top:8px;font-size:12px;color:#aaa;">
                    Payment ID: <?= $order['razorpay_payment_id'] ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>
</body>
</html>
