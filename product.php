<?php
require_once 'config.php';
$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect('index.php');
$result = mysqli_query($conn, "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = $id");
if (mysqli_num_rows($result) === 0) redirect('index.php');
$p = mysqli_fetch_assoc($result);
$discount = discountPercent($p['old_price'], $p['price']);
$related = mysqli_query($conn, "SELECT * FROM products WHERE category_id = {$p['category_id']} AND id != $id LIMIT 4");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $p['name'] ?> - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<nav class="navbar">
    <div class="nav-container">
        <a href="index.php" class="logo"><i class="fas fa-shopping-bag"></i> <?= SITE_NAME ?></a>
        <div class="nav-icons">
            <?php if(isLoggedIn()): ?>
                <a href="cart.php" class="cart-icon"><i class="fas fa-shopping-cart"></i><span class="cart-badge"><?= getCartCount() ?></span></a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i></a>
            <?php else: ?>
                <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                <a href="register.php" class="btn-register">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="product-detail container">
    <div style="padding:16px 0; font-size:14px; color:#888;">
        <a href="index.php" style="color:#2874f0;">Home</a> → <?= $p['name'] ?>
    </div>
    <div class="product-detail-grid">
        <div class="product-detail-img">
            <i class="fas fa-image" style="font-size:120px; color:#2874f0; opacity:0.2;"></i>
        </div>
        <div class="product-detail-info">
            <h1><?= $p['name'] ?></h1>
            <div class="rating" style="margin:12px 0;">
                <?php for($i=1;$i<=5;$i++): ?>
                    <i class="fas fa-star <?= $i<=$p['rating']?'active':'' ?>" style="font-size:18px;"></i>
                <?php endfor; ?>
                <span style="font-size:14px;color:#888;">(<?= $p['rating'] ?>)</span>
            </div>
            <div style="margin-bottom:16px;">
                <span class="price" style="font-size:36px;"><?= formatPrice($p['price']) ?></span>
                <?php if($p['old_price']): ?>
                    <span class="old-price" style="font-size:18px;"><?= formatPrice($p['old_price']) ?></span>
                    <span style="background:#e8f5e9;color:#2e7d32;padding:4px 10px;border-radius:4px;font-size:13px;font-weight:700;margin-left:8px;">
                        Save <?= formatPrice($p['old_price']-$p['price']) ?>
                    </span>
                <?php endif; ?>
            </div>
            <p style="color:#555;line-height:1.8;margin-bottom:20px;"><?= $p['description'] ?></p>
            <div style="background:#f8f9fa;padding:16px;border-radius:8px;margin-bottom:20px;display:flex;gap:20px;">
                <div><i class="fas fa-check-circle" style="color:#26a541;"></i> In Stock</div>
                <div><i class="fas fa-truck" style="color:#2874f0;"></i> Free Delivery</div>
                <div><i class="fas fa-undo" style="color:#ff6161;"></i> Easy Returns</div>
            </div>
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                <label style="font-weight:600;">Qty:</label>
                <button class="qty-btn" onclick="changeDetailQty(-1)">−</button>
                <input type="number" id="detail_qty" value="1" min="1" max="10" style="width:60px;text-align:center;padding:8px;border:2px solid #ddd;border-radius:8px;">
                <button class="qty-btn" onclick="changeDetailQty(1)">+</button>
            </div>
            <button class="btn-add-cart" onclick="addToCartDetail(<?= $p['id'] ?>)">
                <i class="fas fa-cart-plus"></i> Add to Cart
            </button>
            <a href="checkout.php">
                <button class="btn-buy-now"><i class="fas fa-bolt"></i> Buy Now</button>
            </a>
        </div>
    </div>
    <?php if(mysqli_num_rows($related) > 0): ?>
    <div style="margin-top:50px;">
        <h2 class="section-title" style="text-align:left;">Related Products</h2>
        <div class="product-grid">
            <?php while($rp = mysqli_fetch_assoc($related)): ?>
            <div class="product-card">
                <div class="product-img"><i class="fas fa-image img-placeholder"></i></div>
                <div class="product-info">
                    <h3><?= $rp['name'] ?></h3>
                    <div class="price-row">
                        <span class="price"><?= formatPrice($rp['price']) ?></span>
                        <?php if($rp['old_price']): ?><span class="old-price"><?= formatPrice($rp['old_price']) ?></span><?php endif; ?>
                    </div>
                    <div class="product-actions">
                        <a href="product.php?id=<?= $rp['id'] ?>" class="btn-view">View</a>
                        <button class="btn-cart" onclick="addToCart(<?= $rp['id'] ?>)"><i class="fas fa-cart-plus"></i> Add</button>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
<script src="assets/js/main.js"></script>
<script>
function changeDetailQty(change) {
    const input = document.getElementById('detail_qty');
    let qty = parseInt(input.value) + change;
    if (qty < 1) qty = 1;
    if (qty > 10) qty = 10;
    input.value = qty;
}
function addToCartDetail(productId) {
    const qty = document.getElementById('detail_qty').value;
    addToCart(productId, parseInt(qty));
}
</script>
</body>
</html>
