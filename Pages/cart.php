<?php
session_start();
require_once __DIR__ . '/../ClassAutoLoad.php';
require_once __DIR__ . '/../DBConnection.php';

$components = new Components();
$db = new Database($conf);
$db->connect();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_GET['add']) && isset($_GET['id'])) {
    $itemId = intval($_GET['id']);
    
    $item = $db->fetch("SELECT * FROM items WHERE id = ?", [$itemId]);
    
    if ($item && count($item) > 0) {
        $item = $item[0];
        
        if (isset($_SESSION['cart'][$itemId])) {
            $_SESSION['cart'][$itemId]['qty']++;
        } else {
            $_SESSION['cart'][$itemId] = [
                'id' => $item['id'],
                'name' => $item['item_name'],
                'price' => $item['Price'],
                'image' => $item['ImageUrl'],
                'condition' => $item['item_condition'],
                'category' => $item['item_category'],
                'qty' => 1,
                'max_qty' => $item['quantity']
            ];
        }
        
        header('Location: cart.php?added=1');
        exit;
    }
}

if (isset($_GET['remove'])) {
    $itemId = intval($_GET['remove']);
    unset($_SESSION['cart'][$itemId]);
    header('Location: cart.php?removed=1');
    exit;
}

// Handle update quantity
// if (isset($_POST['update_cart'])) {
//     foreach ($_POST['quantity'] as $itemId => $qty) {
//         $qty = intval($qty);
//         $itemId = intval($itemId);
        
//         if ($qty <= 0) {
//             unset($_SESSION['cart'][$itemId]);
//         } else {
//             if (isset($_SESSION['cart'][$itemId])) {
//                 $maxQty = $_SESSION['cart'][$itemId]['max_qty'];
//                 $_SESSION['cart'][$itemId]['qty'] = min($qty, $maxQty);
//             }
//         }
//     }
//     header('Location: cart.php?updated=1');
//     exit;
// }

// Handle clear cart
if (isset($_GET['clear'])) {
    $_SESSION['cart'] = [];
    header('Location: cart.php?cleared=1');
    exit;
}

// Calculate totals
$cartTotal = 0;
$cartCount = 0;
foreach ($_SESSION['cart'] as $item) {
    $cartTotal += $item['price'] * $item['qty'];
    $cartCount += $item['qty'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - StrathMart</title>
    <style>
        :root {
            --bg-primary: #ffffff;
            --bg-secondary: #f5f5f5;
            --bg-tertiary: #f8f9fa;
            --text-primary: #1a1a1a;
            --text-secondary: #555;
            --text-tertiary: #666;
            --border-color: #e9ecef;
            --card-shadow: rgba(0, 0, 0, 0.1);
            --accent-color: #007bff;
            --accent-hover: #0056b3;
            --danger-color: #dc3545;
            --danger-hover: #c82333;
            --transition: 0.3s ease;
        }

        [data-theme="dark"] {
            --bg-primary: #1a1a1a;
            --bg-secondary: #121212;
            --bg-tertiary: #242424;
            --text-primary: #e0e0e0;
            --text-secondary: #b0b0b0;
            --text-tertiary: #999;
            --border-color: #333;
            --card-shadow: rgba(0, 0, 0, 0.3);
            --accent-color: #4a9eff;
            --accent-hover: #357abd;
            --danger-color: #ff4757;
            --danger-hover: #ee5a6f;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-secondary);
            color: var(--text-primary);
            transition: all var(--transition);
        }

        /* Dark Mode Toggle */
        .theme-toggle {
            position: fixed;
            top: 100px;
            right: 30px;
            z-index: 1000;
            background: var(--bg-primary);
            border: 2px solid var(--border-color);
            border-radius: 50px;
            padding: 10px 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 12px var(--card-shadow);
            transition: all var(--transition);
        }

        .theme-toggle:hover {
            transform: scale(1.05);
        }

        .theme-toggle svg {
            width: 20px;
            height: 20px;
            fill: var(--accent-color);
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        h1 {
            font-size: 2.5rem;
            color: var(--text-primary);
            font-weight: 800;
        }

        .cart-count {
            background: linear-gradient(135deg, var(--accent-color), var(--accent-hover));
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 700;
        }

        /* Alerts */
        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideDown 0.4s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-info {
            background: linear-gradient(135deg, #d1ecf1, #bee5eb);
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        /* Cart Layout */
        .cart-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }

        .cart-items {
            background: var(--bg-primary);
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 4px 20px var(--card-shadow);
        }

        .cart-items h2 {
            font-size: 1.5rem;
            margin-bottom: 25px;
            color: var(--text-primary);
            font-weight: 700;
        }

        /* Cart Item */
        .cart-item {
            display: grid;
            grid-template-columns: 100px 1fr auto;
            gap: 20px;
            padding: 20px;
            margin-bottom: 20px;
            background: var(--bg-tertiary);
            border-radius: 12px;
            border: 2px solid var(--border-color);
            transition: all var(--transition);
        }

        .cart-item:hover {
            border-color: var(--accent-color);
            box-shadow: 0 4px 12px var(--card-shadow);
        }

        .item-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 2px 8px var(--card-shadow);
        }

        .item-details {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .item-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .item-meta {
            font-size: 0.9rem;
            color: var(--text-tertiary);
            margin-bottom: 5px;
        }

        .item-price {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--accent-color);
            margin-top: 10px;
        }

        .item-actions {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: flex-end;
        }

        .qty-control {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .qty-btn {
            width: 35px;
            height: 35px;
            border: 2px solid var(--border-color);
            background: var(--bg-primary);
            color: var(--text-primary);
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.2rem;
            font-weight: 700;
            transition: all var(--transition);
        }

        .qty-btn:hover {
            border-color: var(--accent-color);
            color: var(--accent-color);
            transform: scale(1.1);
        }

        .qty-input {
            width: 60px;
            text-align: center;
            padding: 8px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            background: var(--bg-primary);
            color: var(--text-primary);
        }

        .remove-btn {
            padding: 8px 16px;
            background: transparent;
            border: 2px solid var(--danger-color);
            color: var(--danger-color);
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all var(--transition);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .remove-btn:hover {
            background: var(--danger-color);
            color: white;
            transform: translateY(-2px);
        }

        /* Cart Summary */
        .cart-summary {
            background: var(--bg-primary);
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 4px 20px var(--card-shadow);
            height: fit-content;
            position: sticky;
            top: 20px;
        }

        .cart-summary h2 {
            font-size: 1.5rem;
            margin-bottom: 25px;
            color: var(--text-primary);
            font-weight: 700;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .summary-row:last-of-type {
            border-bottom: 2px solid var(--border-color);
            margin-bottom: 15px;
        }

        .summary-label {
            color: var(--text-tertiary);
            font-weight: 500;
        }

        .summary-value {
            font-weight: 700;
            color: var(--text-primary);
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 20px 0;
            font-size: 1.4rem;
            font-weight: 800;
        }

        .total-value {
            background: linear-gradient(135deg, var(--accent-color), var(--accent-hover));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .btn {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
            margin-bottom: 15px;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.5);
        }

        .btn-secondary {
            background: var(--bg-tertiary);
            color: var(--text-secondary);
            border: 2px solid var(--border-color);
            margin-bottom: 15px;
        }

        .btn-secondary:hover {
            background: var(--bg-secondary);
            border-color: var(--accent-color);
        }

        .btn-danger {
            background: transparent;
            border: 2px solid var(--danger-color);
            color: var(--danger-color);
        }

        .btn-danger:hover {
            background: var(--danger-color);
            color: white;
        }

        /* Empty Cart */
        .empty-cart {
            text-align: center;
            padding: 80px 20px;
            background: var(--bg-primary);
            border-radius: 16px;
            box-shadow: 0 4px 20px var(--card-shadow);
        }

        .empty-cart svg {
            margin-bottom: 30px;
            opacity: 0.5;
        }

        .empty-cart h2 {
            font-size: 2rem;
            margin-bottom: 15px;
            color: var(--text-primary);
        }

        .empty-cart p {
            font-size: 1.1rem;
            color: var(--text-tertiary);
            margin-bottom: 30px;
        }

        @media (max-width: 968px) {
            .theme-toggle {
                top: 80px;
                right: 15px;
                padding: 8px 16px;
            }

            .cart-layout {
                grid-template-columns: 1fr;
            }

            .cart-item {
                grid-template-columns: 80px 1fr;
                gap: 15px;
            }

            .item-image {
                width: 80px;
                height: 80px;
            }

            .item-actions {
                grid-column: 1 / -1;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                margin-top: 15px;
            }

            h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <?php $components->header(); ?>

    <!-- Dark Mode Toggle
    <div class="theme-toggle" onclick="toggleTheme()">
        <svg id="theme-icon-sun" viewBox="0 0 24 24" style="display: none;">
            <circle cx="12" cy="12" r="5"/>
            <line x1="12" y1="1" x2="12" y2="3" stroke="currentColor" stroke-width="2"/>
            <line x1="12" y1="21" x2="12" y2="23" stroke="currentColor" stroke-width="2"/>
            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64" stroke="currentColor" stroke-width="2"/>
            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78" stroke="currentColor" stroke-width="2"/>
            <line x1="1" y1="12" x2="3" y2="12" stroke="currentColor" stroke-width="2"/>
            <line x1="21" y1="12" x2="23" y2="12" stroke="currentColor" stroke-width="2"/>
            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36" stroke="currentColor" stroke-width="2"/>
            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22" stroke="currentColor" stroke-width="2"/>
        </svg>
        <svg id="theme-icon-moon" viewBox="0 0 24 24">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
        </svg>
        <span class="theme-toggle-text">Dark Mode</span>
    </div> -->

    <div class="container">
        <div class="page-header">
            <h1>Shopping Cart</h1>
            <?php if ($cartCount > 0): ?>
            <div class="cart-count"><?php echo $cartCount; ?> item<?php echo $cartCount > 1 ? 's' : ''; ?></div>
            <?php endif; ?>
        </div>

        <?php if (isset($_GET['added'])): ?>
        <div class="alert alert-success">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            <span>Item added to cart successfully!</span>
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['removed'])): ?>
        <div class="alert alert-info">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <span>Item removed from cart.</span>
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['updated'])): ?>
        <div class="alert alert-success">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            <span>Cart updated successfully!</span>
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['cleared'])): ?>
        <div class="alert alert-info">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <span>Cart cleared.</span>
        </div>
        <?php endif; ?>

        <?php if (empty($_SESSION['cart'])): ?>
            <div class="empty-cart">
                <svg width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
                <h2>Your cart is empty</h2>
                <p>Add some items to get started!</p>
                <a href="home.php" class="btn btn-primary" style="max-width: 300px; margin: 0 auto;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    Start Shopping
                </a>
            </div>
        <?php else: ?>
            <form method="POST" action="">
                <div class="cart-layout">
                    <div class="cart-items">
                        <h2>Cart Items</h2>
                        
                        <?php foreach ($_SESSION['cart'] as $itemId => $item): ?>
                        <div class="cart-item">
                            <img src="<?php echo htmlspecialchars($item['image'] ?: 'images/placeholder.png'); ?>" 
                                 alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                 class="item-image"
                                 onerror="this.src='images/placeholder.png'">
                            
                            <div class="item-details">
                                <div class="item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                <div class="item-meta">Condition: <strong><?php echo htmlspecialchars($item['condition']); ?></strong></div>
                                <div class="item-meta">Category: <strong><?php echo htmlspecialchars($item['category']); ?></strong></div>
                                <div class="item-price">Ksh. <?php echo number_format($item['price'], 2); ?></div>
                            </div>
                            
                            <div class="item-actions">
                                <div class="qty-control">
                                    <button type="button" class="qty-btn" onclick="decreaseQty(<?php echo $itemId; ?>)">-</button>
                                    <input type="number" 
                                           name="quantity[<?php echo $itemId; ?>]" 
                                           value="<?php echo $item['qty']; ?>" 
                                           min="1" 
                                           max="<?php echo $item['max_qty']; ?>"
                                           class="qty-input" 
                                           id="qty-<?php echo $itemId; ?>"
                                           readonly>
                                    <button type="button" class="qty-btn" onclick="increaseQty(<?php echo $itemId; ?>, <?php echo $item['max_qty']; ?>)">+</button>
                                </div>
                                
                                <a href="?remove=<?php echo $itemId; ?>" class="remove-btn" onclick="return confirm('Remove this item from cart?')">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                    Remove
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>

                        
                    </div>

                    <div class="cart-summary">
                        <h2>Order Summary</h2>
                        
                        <div class="summary-row">
                            <span class="summary-label">Subtotal</span>
                            <span class="summary-value">Ksh. <?php echo number_format($cartTotal, 2); ?></span>
                        </div>
                        
                        <div class="summary-row">
                            <span class="summary-label">Service Fee</span>
                            <span class="summary-value">Ksh. 0.00</span>
                        </div>
                        
                        <div class="summary-row">
                            <span class="summary-label">Items</span>
                            <span class="summary-value"><?php echo $cartCount; ?></span>
                        </div>
                        
                        <div class="total-row">
                            <span>Total</span>
                            <span class="total-value">Ksh. <?php echo number_format($cartTotal, 2); ?></span>
                        </div>
                        
                        <a href="checkout.php?id=<?php echo $item["id"]; ?>" class="btn btn-primary">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Proceed to Checkout
                        </a>
                        
                        <a href="home.php" class="btn btn-secondary">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="19" y1="12" x2="5" y2="12"></line>
                                <polyline points="12 19 5 12 12 5"></polyline>
                            </svg>
                            Continue Shopping
                        </a>
                        
                        <a href="?clear=1" class="btn btn-danger" onclick="return confirm('Clear entire cart?')">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                            Clear Cart
                        </a>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <script>
        // Dark Mode Toggle
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            const sunIcon = document.getElementById('theme-icon-sun');
            const moonIcon = document.getElementById('theme-icon-moon');
            const toggleText = document.querySelector('.theme-toggle-text');
            
            if (newTheme === 'dark') {
                sunIcon.style.display = 'block';
                moonIcon.style.display = 'none';
                toggleText.textContent = 'Light Mode';
            } else {
                sunIcon.style.display = 'none';
                moonIcon.style.display = 'block';
                toggleText.textContent = 'Dark Mode';
            }
        }

        // Initialize theme
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
            
            const sunIcon = document.getElementById('theme-icon-sun');
            const moonIcon = document.getElementById('theme-icon-moon');
            const toggleText = document.querySelector('.theme-toggle-text');
            
            if (savedTheme === 'dark') {
                sunIcon.style.display = 'block';
                moonIcon.style.display = 'none';
                toggleText.textContent = 'Light Mode';
            }
        })();

        // Quantity controls
        function increaseQty(itemId, maxQty) {
            const input = document.getElementById('qty-' + itemId);
            const currentQty = parseInt(input.value);
            if (currentQty < maxQty) {
                input.value = currentQty + 1;
            }
        }

        function decreaseQty(itemId) {
            const input = document.getElementById('qty-' + itemId);
            const currentQty = parseInt(input.value);
            if (currentQty > 1) {
                input.value = currentQty - 1;
            }
        }

        // Auto-hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Press 'D' to toggle dark mode
            if (e.key === 'd' || e.key === 'D') {
                if (!e.target.matches('input, textarea')) {
                    toggleTheme();
                }
            }
        });
    </script>
</body>
</html>