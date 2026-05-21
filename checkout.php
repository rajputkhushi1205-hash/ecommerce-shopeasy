<?php
require_once 'config.php';
if (!isLoggedIn()) redirect('login.php');

$user_id = $_SESSION['user_id'];

// Get cart items
$cart_items = mysqli_query($conn, "
    SELECT c.id as cart_id, c.quantity, p.id as product_id, p.name, p.price
    FROM cart c JOIN products p ON c.product_id = p.id
    WHERE c.user_id = $user_id
");

$items = [];
$total = 0;
while ($item = mysqli_fetch_assoc($cart_items)) {
    $item['subtotal'] = $item['price'] * $item['quantity'];
    $total += $item['subtotal'];
    $items[] = $item;
}

if (empty($items)) redirect('cart.php');

// Get user details
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id"));

// Create Razorpay Order
$razorpay_order_id = '';
$key_id = RAZORPAY_KEY_ID;
$key_secret = RAZORPAY_KEY_SECRET;

$api_data = json_encode([
    'amount'   => (int)($total * 100), // Amount in paise
    'currency' => 'INR',
    'receipt'  => 'order_' . time(),
]);

$ch = curl_init('https://api.razorpay.com/v1/orders');
curl_setopt($ch, CURLOPT_USERPWD, "$key_id:$key_secret");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $api_data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
curl_close($ch);

$order = json_decode($response, true);
$razorpay_order_id = $order['id'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>
<nav class="navbar">
    <div class="nav-container">
        <a href="index.php" class="logo"><i class="fas fa-shopping-bag"></i> <?= SITE_NAME ?></a>
        <div class="nav-icons">
            <a href="cart.php"><i class="fas fa-shopping-cart"></i> Back to Cart</a>
        </div>
    </div>
</nav>

<div class="container" style="padding:40px 0;">
    <h2 style="margin-bottom:24px;"><i class="fas fa-lock"></i> Secure Checkout</h2>

    <div style="display:grid; grid-template-columns:1fr 380px; gap:30px;">

        <!-- Delivery Address -->
        <div>
            <div style="background:white;border-radius:10px;padding:24px;box-shadow:0 2px 15px rgba(0,0,0,0.1);margin-bottom:20px;">
                <h3 style="margin-bottom:16px; color:#1a237e;"><i class="fas fa-map-marker-alt"></i> Delivery Address</h3>
                <form id="checkoutForm">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" value="<?= $user['name'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" value="<?= $user['phone'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= $user['email'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Delivery Address</label>
                        <textarea name="address" rows="3" placeholder="House no, Street, Area, City, State, PIN" required style="width:100%;padding:12px;border:2px solid #eee;border-radius:8px;font-family:Poppins,sans-serif;"><?= $user['address'] ?></textarea>
                    </div>
                </form>
            </div>

            <!-- Order Items -->
            <div style="background:white;border-radius:10px;padding:24px;box-shadow:0 2px 15px rgba(0,0,0,0.1);">
                <h3 style="margin-bottom:16px; color:#1a237e;"><i class="fas fa-box"></i> Order Items</h3>
                <?php foreach($items as $item): ?>
                <div style="display:flex;justify-content:space-between;padding:12px 0;border-bottom:1px solid #eee;">
                    <div>
                        <strong><?= $item['name'] ?></strong>
                        <span style="color:#888;font-size:13px;"> × <?= $item['quantity'] ?></span>
                    </div>
                    <strong><?= formatPrice($item['subtotal']) ?></strong>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Order Summary -->
        <div>
            <div style="background:white;border-radius:10px;padding:24px;box-shadow:0 2px 15px rgba(0,0,0,0.1);position:sticky;top:100px;">
                <h3 style="margin-bottom:16px; color:#1a237e;">Order Summary</h3>

                <div style="display:flex;justify-content:space-between;margin-bottom:10px;">
                    <span>Subtotal</span><span><?= formatPrice($total) ?></span>
                </div>
                <div style="display:flex;justify-content:space-between;margin-bottom:10px;color:#26a541;">
                    <span>Delivery</span><span>FREE</span>
                </div>
                <hr style="margin:16px 0;">
                <div style="display:flex;justify-content:space-between;font-size:22px;font-weight:700;color:#2874f0;margin-bottom:24px;">
                    <span>Total</span><span><?= formatPrice($total) ?></span>
                </div>

                <!-- Razorpay Button -->
                <button id="payBtn" onclick="payWithRazorpay()" style="width:100%;padding:16px;background:#26a541;color:white;border:none;border-radius:8px;font-size:18px;font-weight:700;cursor:pointer;font-family:Poppins,sans-serif;">
                    <i class="fas fa-lock"></i> Pay <?= formatPrice($total) ?>
                </button>

                <div style="text-align:center;margin-top:12px;font-size:12px;color:#888;">
                    <i class="fas fa-shield-alt" style="color:#26a541;"></i>
                    100% Secure Payment via Razorpay
                </div>

                <div style="display:flex;justify-content:center;gap:10px;margin-top:12px;opacity:0.6;">
                    <i class="fab fa-cc-visa" style="font-size:28px;"></i>
                    <i class="fab fa-cc-mastercard" style="font-size:28px;"></i>
                    <i class="fas fa-university" style="font-size:24px;"></i>
                    <i class="fas fa-wallet" style="font-size:24px;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function payWithRazorpay() {
    const name    = document.querySelector('[name="name"]').value;
    const phone   = document.querySelector('[name="phone"]').value;
    const email   = document.querySelector('[name="email"]').value;
    const address = document.querySelector('[name="address"]').value;

    if (!name || !phone || !address) {
        alert('Please fill all delivery details!');
        return;
    }

    const options = {
        key: '<?= RAZORPAY_KEY_ID ?>',
        amount: <?= (int)($total * 100) ?>,
        currency: 'INR',
        name: '<?= SITE_NAME ?>',
        description: 'Order Payment',
        order_id: '<?= $razorpay_order_id ?>',
        prefill: { name, email, contact: phone },
        theme: { color: '#2874f0' },
        handler: function(response) {
            // Payment successful - save order
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'payment_success.php';

            const fields = {
                razorpay_payment_id: response.razorpay_payment_id,
                razorpay_order_id:   response.razorpay_order_id || '<?= $razorpay_order_id ?>',
                razorpay_signature:  response.razorpay_signature || '',
                address:             address,
                amount:              '<?= $total ?>'
            };

            for (const [key, value] of Object.entries(fields)) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = value;
                form.appendChild(input);
            }

            document.body.appendChild(form);
            form.submit();
        },
        modal: {
            ondismiss: function() {
                alert('Payment cancelled. Please try again.');
            }
        }
    };

    const rzp = new Razorpay(options);
    rzp.open();
}
</script>
</body>
</html>
