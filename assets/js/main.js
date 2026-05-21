// ── Add to Cart ────────────────────────────────────────────
function addToCart(productId, quantity = 1) {
    fetch('ajax/cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=add&product_id=${productId}&quantity=${quantity}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast('✅ Added to cart!');
            updateCartBadge(data.cart_count);
        } else if (data.redirect) {
            window.location.href = data.redirect;
        } else {
            showToast('❌ ' + data.message);
        }
    })
    .catch(() => showToast('❌ Something went wrong!'));
}

// ── Update Cart Badge ──────────────────────────────────────
function updateCartBadge(count) {
    const badge = document.querySelector('.cart-badge');
    if (badge) {
        badge.textContent = count;
        badge.style.transform = 'scale(1.3)';
        setTimeout(() => badge.style.transform = 'scale(1)', 300);
    }
}

// ── Remove from Cart ───────────────────────────────────────
function removeFromCart(cartId) {
    if (!confirm('Remove this item from cart?')) return;
    fetch('ajax/cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=remove&cart_id=${cartId}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

// ── Show Toast Notification ────────────────────────────────
function showToast(message) {
    let toast = document.getElementById('toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'toast';
        toast.className = 'toast';
        document.body.appendChild(toast);
    }
    toast.textContent = message;
    toast.style.display = 'block';
    setTimeout(() => { toast.style.display = 'none'; }, 3000);
}

// ── Search Products ────────────────────────────────────────
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            window.location.href = `products.php?search=${this.value}`;
        }
    });
}

// ── Quantity Controls ──────────────────────────────────────
function changeQty(cartId, change) {
    const input = document.getElementById(`qty_${cartId}`);
    let qty = parseInt(input.value) + change;
    if (qty < 1) qty = 1;
    if (qty > 10) qty = 10;
    input.value = qty;

    fetch('ajax/cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=update&cart_id=${cartId}&quantity=${qty}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) location.reload();
    });
}
