<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> - Shop Everything</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<!-- ── NAVBAR ─────────────────────────────────────────────── -->
<nav class="navbar">
    <div class="nav-container">
        <a href="index.php" class="logo">
            <i class="fas fa-shopping-bag"></i> <?= SITE_NAME ?>
        </a>

        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search for products...">
            <button><i class="fas fa-search"></i></button>
        </div>

        <div class="nav-icons">
            <?php if(isLoggedIn()): ?>
                <a href="profile.php" title="Profile">
                    <i class="fas fa-user"></i>
                    <span><?= $_SESSION['user_name'] ?></span>
                </a>
                <a href="cart.php" title="Cart" class="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge"><?= getCartCount() ?></span>
                </a>
                <a href="logout.php" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            <?php else: ?>
                <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                <a href="register.php" class="btn-register">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- ── HERO BANNER ────────────────────────────────────────── -->
<div class="hero">
    <div class="hero-content">
        <h1>Welcome to <?= SITE_NAME ?></h1>
        <p>Discover amazing products at unbeatable prices</p>
        <a href="#products" class="btn-hero">Shop Now <i class="fas fa-arrow-right"></i></a>
    </div>
</div>

<!-- ── CATEGORIES ─────────────────────────────────────────── -->
<section class="categories">
    <div class="container">
        <h2 class="section-title">Shop by Category</h2>
        <div class="category-grid">
            <?php
            $cats = mysqli_query($conn, "SELECT * FROM categories");
            while($cat = mysqli_fetch_assoc($cats)):
            ?>
            <a href="products.php?category=<?= $cat['id'] ?>" class="category-card">
                <div class="cat-icon">
                    <?php
                    $icons = ['Electronics' => 'fa-laptop', 'Fashion' => 'fa-tshirt', 'Home & Kitchen' => 'fa-home', 'Books' => 'fa-book'];
                    $icon = $icons[$cat['name']] ?? 'fa-tag';
                    ?>
                    <i class="fas <?= $icon ?>"></i>
                </div>
                <span><?= $cat['name'] ?></span>
            </a>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- ── FEATURED PRODUCTS ──────────────────────────────────── -->
<section class="products" id="products">
    <div class="container">
        <h2 class="section-title">Featured Products</h2>
        <div class="product-grid">
            <?php
            $products = mysqli_query($conn, "SELECT * FROM products WHERE featured = 1 LIMIT 8");
            while($p = mysqli_fetch_assoc($products)):
                $discount = discountPercent($p['old_price'], $p['price']);
            ?>
            <div class="product-card">
                <?php if($discount > 0): ?>
                    <div class="badge"><?= $discount ?>% OFF</div>
                <?php endif; ?>

                <div class="product-img">
                    <i class="fas fa-image img-placeholder"></i>
                </div>

                <div class="product-info">
                    <h3><?= $p['name'] ?></h3>
                    <p class="description"><?= substr($p['description'], 0, 60) ?>...</p>

                    <div class="rating">
                        <?php for($i=1; $i<=5; $i++): ?>
                            <i class="fas fa-star <?= $i <= $p['rating'] ? 'active' : '' ?>"></i>
                        <?php endfor; ?>
                        <span>(<?= $p['rating'] ?>)</span>
                    </div>

                    <div class="price-row">
                        <span class="price"><?= formatPrice($p['price']) ?></span>
                        <?php if($p['old_price']): ?>
                            <span class="old-price"><?= formatPrice($p['old_price']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="product-actions">
                        <a href="product.php?id=<?= $p['id'] ?>" class="btn-view">View Details</a>
                        <button class="btn-cart" onclick="addToCart(<?= $p['id'] ?>)">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- ── ALL PRODUCTS ───────────────────────────────────────── -->
<section class="products" style="background:#f8f9fa;">
    <div class="container">
        <h2 class="section-title">All Products</h2>
        <div class="product-grid">
            <?php
            $all = mysqli_query($conn, "SELECT * FROM products ORDER BY created_at DESC");
            while($p = mysqli_fetch_assoc($all)):
                $discount = discountPercent($p['old_price'], $p['price']);
            ?>
            <div class="product-card">
                <?php if($discount > 0): ?>
                    <div class="badge"><?= $discount ?>% OFF</div>
                <?php endif; ?>

                <div class="product-img">
                    <i class="fas fa-image img-placeholder"></i>
                </div>

                <div class="product-info">
                    <h3><?= $p['name'] ?></h3>
                    <p class="description"><?= substr($p['description'], 0, 60) ?>...</p>

                    <div class="rating">
                        <?php for($i=1; $i<=5; $i++): ?>
                            <i class="fas fa-star <?= $i <= $p['rating'] ? 'active' : '' ?>"></i>
                        <?php endfor; ?>
                        <span>(<?= $p['rating'] ?>)</span>
                    </div>

                    <div class="price-row">
                        <span class="price"><?= formatPrice($p['price']) ?></span>
                        <?php if($p['old_price']): ?>
                            <span class="old-price"><?= formatPrice($p['old_price']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="product-actions">
                        <a href="product.php?id=<?= $p['id'] ?>" class="btn-view">View Details</a>
                        <button class="btn-cart" onclick="addToCart(<?= $p['id'] ?>)">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- ── FOOTER ─────────────────────────────────────────────── -->
<footer>
    <div class="container">
        <div class="footer-grid">
            <div>
                <h3><i class="fas fa-shopping-bag"></i> <?= SITE_NAME ?></h3>
                <p>Your one-stop shop for everything you need at the best prices.</p>
            </div>
            <div>
                <h4>Quick Links</h4>
                <a href="index.php">Home</a>
                <a href="products.php">Products</a>
                <a href="cart.php">Cart</a>
                <a href="orders.php">My Orders</a>
            </div>
            <div>
                <h4>Contact</h4>
                <p><i class="fas fa-envelope"></i> support@shopeasy.com</p>
                <p><i class="fas fa-phone"></i> +91 98765 43210</p>
                <p><i class="fas fa-map-marker-alt"></i> Indore, M.P.</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 <?= SITE_NAME ?>. Built by Pratiksha Singh | Razorpay Secured Payments</p>
        </div>
    </div>
</footer>

<script src="assets/js/main.js"></script>
</body>
</html>
