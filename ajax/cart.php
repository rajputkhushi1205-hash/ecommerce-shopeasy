<?php
require_once '../config.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

// ── Add to Cart ────────────────────────────────────────────
if ($action === 'add') {
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'redirect' => SITE_URL . '/login.php']);
        exit;
    }

    $user_id    = $_SESSION['user_id'];
    $product_id = (int)$_POST['product_id'];
    $quantity   = (int)($_POST['quantity'] ?? 1);

    // Check if already in cart
    $check = mysqli_query($conn, "SELECT id, quantity FROM cart WHERE user_id=$user_id AND product_id=$product_id");

    if (mysqli_num_rows($check) > 0) {
        $row = mysqli_fetch_assoc($check);
        $new_qty = $row['quantity'] + $quantity;
        mysqli_query($conn, "UPDATE cart SET quantity=$new_qty WHERE id={$row['id']}");
    } else {
        mysqli_query($conn, "INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id, $product_id, $quantity)");
    }

    $cart_count = getCartCount();
    echo json_encode(['success' => true, 'cart_count' => $cart_count]);
}

// ── Remove from Cart ───────────────────────────────────────
elseif ($action === 'remove') {
    if (!isLoggedIn()) { echo json_encode(['success' => false]); exit; }
    $cart_id = (int)$_POST['cart_id'];
    $user_id = $_SESSION['user_id'];
    mysqli_query($conn, "DELETE FROM cart WHERE id=$cart_id AND user_id=$user_id");
    echo json_encode(['success' => true]);
}

// ── Update Quantity ────────────────────────────────────────
elseif ($action === 'update') {
    if (!isLoggedIn()) { echo json_encode(['success' => false]); exit; }
    $cart_id  = (int)$_POST['cart_id'];
    $quantity = (int)$_POST['quantity'];
    $user_id  = $_SESSION['user_id'];
    if ($quantity < 1) $quantity = 1;
    mysqli_query($conn, "UPDATE cart SET quantity=$quantity WHERE id=$cart_id AND user_id=$user_id");
    echo json_encode(['success' => true]);
}
?>
