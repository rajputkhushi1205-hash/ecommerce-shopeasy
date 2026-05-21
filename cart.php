<?php
require_once 'config.php';
if (!isLoggedIn()) redirect('login.php');

$user_id = $_SESSION['user_id'];

// Fetch cart items
$cart_items = mysqli_query($conn, "
    SELECT c.id as cart_id, c.quantity, p.id as product_id,
           p.name, p.price, p.old_price, p.image
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = $user_id
");

$total = 0;
$items = [];
while ($item = mysqli_fetch_assoc($cart_items)) {
    $item['subtotal'] = $item['price'] * $item['quantity'];
    $total += $item['subtotal'];
    $items[] = $item;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cart - <?= SITE_NAME ?></title>
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
            <a href="orders.php"><i class="fas fa-box"></i> Orders</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </div>
</nav>

<div class="cart-container container">
    <h2 class="section-title" style="text-align:left; margin-bottom:24px;">
        <i class="fas fa-shopping-cart"></i> My Cart (<?= count($items) ?> items)
    </h2>

    <?php if (empty($items)): ?>
        <div style="text-align:center; padding:60px; background:white; border-radius:10px;">
            <i class="fas fa-shopping-cart" style="font-size:80px; color:#ddd; margin-bottom:20px; display:block;"></i>
            <h3>Your cart is empty!</h3>
            <a href="index.php" style="color:#2874f0; font-weight:600;">Continue Shopping →</a>
        </div>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $item): ?>
                <tr>
                    <td>
                        <strong><?= $item['name'] ?></strong>
                    </td>
                    <td><?= formatPrice($item['price']) ?></td>
                    <td>
                        <button class="qty-btn" onclick="changeQty(<?= $item['cart_id'] ?>, -1)">−</button>
                        <input type="number" id="qty_<?= $item['cart_id'] ?>" class="qty-input" value="<?= $item['quantity'] ?>" min="1" max="10" readonly>
                        <button class="qty-btn" onclick="changeQty(<?= $item['cart_id'] ?>, 1)">+</button>
                    </td>
                    <td><strong><?= formatPrice($item['subtotal']) ?></strong></td>
                    <td>
                        <button class="btn-danger" onclick="removeFromCart(<?= $item['cart_id'] ?>)">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="cart-summary">
            <table style="width:100%;">
                <tr>
                    <td style="font-size:16px;">Subtotal:</td>
                    <td style="text-align:right; font-size:16px;"><?= formatPrice($total) ?></td>
                </tr>
                <tr>
                    <td style="color:#26a541;">Free Delivery:</td>
                    <td style="text-align:right; color:#26a541;">- ₹0</td>
                </tr>
                <tr>
                    <td colspan="2"><hr style="margin:12px 0;"></td>
                </tr>
                <tr>
                    <td><strong>Total Amount:</strong></td>
                    <td style="text-align:right;" class="cart-total"><?= formatPrice($total) ?></td>
                </tr>
            </table>

            <a href="checkout.php">
                <button class="btn-checkout">
                    <i class="fas fa-lock"></i> Proceed to Checkout — <?= formatPrice($total) ?>
                </button>
            </a>

            <a href="index.php" style="display:block; text-align:center; margin-top:12px; color:#2874f0;">
                ← Continue Shopping
            </a>
        </div>
    <?php endif; ?>
</div>

<script src="assets/js/main.js"></script>
</body>
</html>
