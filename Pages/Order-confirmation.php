<?php
session_start();
require_once __DIR__ . '/../ClassAutoLoad.php';
require_once __DIR__ . '/../DBConnection.php';

$components = new Components();
$db = new Database($conf);
$db->connect();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['order_success'])) {
    header('Location: index.php');
    exit;
}

// Clear the success flag
unset($_SESSION['order_success']);

$orderId = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($orderId === 0) {
    header('Location: index.php');
    exit;
}


$order = $db->fetch("SELECT 
                        orders.*,
                        items.item_name,
                        items.Price,
                        items.ImageUrl,
                        items.item_condition,
                        items.item_category,
                        items.quantity,
                        users.username,
                        users.email
                     FROM orders 
                     JOIN items ON orders.item_id = items.id 
                     JOIN users ON items.user_id = users.id 
                     WHERE orders.id = ? AND orders.buyer_id = ?", 
                     [$orderId, $_SESSION['user_id']]);

if (!$order || count($order) === 0) {
    header('Location: index.php');
    exit;
}

$order = $order[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed - StrathMart</title>
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
            --success-color: #28a745;
            --success-light: #d4edda;
            --accent-color: #007bff;
            --accent-hover: #0056b3;
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
            --success-color: #4caf50;
            --success-light: #2d4a2f;
            --accent-color: #4a9eff;
            --accent-hover: #357abd;
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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all var(--transition);
        }

        /* Dark Mode Toggle
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
        } */

        .theme-toggle:hover {
            transform: scale(1.05);
        }

        .theme-toggle svg {
            width: 20px;
            height: 20px;
            fill: var(--accent-color);
        }

        .container {
            max-width: 900px;
            margin: 60px auto;
            padding: 0 20px;
            flex: 1;
        }

        .success-card {
            background: var(--bg-primary);
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 8px 30px var(--card-shadow);
            text-align: center;
            animation: slideUp 0.6s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #28a745, #20c997);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            box-shadow: 0 8px 24px rgba(40, 167, 69, 0.3);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        .success-icon svg {
            color: white;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: var(--text-primary);
            font-weight: 800;
        }

        .subtitle {
            font-size: 1.15rem;
            color: var(--text-secondary);
            margin-bottom: 40px;
            line-height: 1.7;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .order-details {
            background: linear-gradient(135deg, var(--bg-tertiary), var(--bg-secondary));
            padding: 35px;
            border-radius: 16px;
            text-align: left;
            margin-bottom: 30px;
            border: 1px solid var(--border-color);
            transition: all var(--transition);
        }

        .order-details h2 {
            font-size: 1.5rem;
            margin-bottom: 25px;
            color: var(--text-primary);
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 16px;
            margin-bottom: 10px;
            background: var(--bg-primary);
            border-radius: 10px;
            transition: all var(--transition);
        }

        .detail-row:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 12px var(--card-shadow);
        }

        .detail-label {
            font-weight: 600;
            color: var(--text-tertiary);
            font-size: 0.95rem;
        }

        .detail-value {
            color: var(--text-primary);
            text-align: right;
            font-weight: 600;
        }

        .item-summary {
            display: flex;
            gap: 25px;
            align-items: center;
            padding: 25px;
            background: var(--bg-primary);
            border: 2px solid var(--border-color);
            border-radius: 16px;
            margin-bottom: 30px;
            transition: all var(--transition);
        }

        .item-summary:hover {
            border-color: var(--accent-color);
            box-shadow: 0 8px 24px var(--card-shadow);
        }

        .item-image {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 4px 12px var(--card-shadow);
        }

        .item-info {
            flex: 1;
            text-align: left;
        }

        .item-info h3 {
            font-size: 1.3rem;
            margin-bottom: 10px;
            color: var(--text-primary);
            font-weight: 700;
        }

        .item-price {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--accent-color), var(--accent-hover));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
        }

        .item-meta {
            font-size: 0.9rem;
            color: var(--text-tertiary);
            margin-top: 5px;
        }

        .next-steps {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            padding: 30px;
            border-radius: 16px;
            border: 1px solid #ffeaa7;
            margin-bottom: 30px;
            text-align: left;
        }

        .next-steps h3 {
            font-size: 1.3rem;
            margin-bottom: 20px;
            color: #856404;
            font-weight: 700;
        }

        .next-steps ol {
            margin-left: 25px;
            color: #856404;
            line-height: 2;
        }

        .next-steps li {
            margin-bottom: 12px;
            font-weight: 500;
        }

        .seller-info {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            padding: 30px;
            border-radius: 16px;
            border: 1px solid #c3e6cb;
            margin-bottom: 30px;
            text-align: left;
        }

        .seller-info h3 {
            font-size: 1.3rem;
            margin-bottom: 20px;
            color: #155724;
            font-weight: 700;
        }

        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            padding: 12px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 8px;
            color: #155724;
            font-weight: 500;
            transition: all var(--transition);
        }

        .contact-item:hover {
            background: rgba(255, 255, 255, 0.8);
            transform: translateX(5px);
        }

        .contact-item svg {
            margin-right: 12px;
        }

        .contact-item a {
            color: #155724;
            text-decoration: none;
            font-weight: 600;
        }

        .contact-item a:hover {
            text-decoration: underline;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 40px;
        }

        .btn {
            padding: 16px 32px;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: all var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.5);
        }

        .btn-secondary {
            background: var(--bg-primary);
            color: var(--accent-color);
            border: 2px solid var(--accent-color);
        }

        .btn-secondary:hover {
            background: var(--accent-color);
            color: white;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 700;
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }

        @media (max-width: 768px) {
            .theme-toggle {
                top: 80px;
                right: 15px;
                padding: 8px 16px;
            }

            .success-card {
                padding: 35px 25px;
            }

            h1 {
                font-size: 2rem;
            }

            .item-summary {
                flex-direction: column;
                text-align: center;
            }

            .item-image {
                width: 100%;
                height: 200px;
            }

            .item-info {
                text-align: center;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php $components->header(); ?>

    <!-- Dark Mode Toggle -->
    <!-- <div class="theme-toggle" onclick="toggleTheme()">
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
        <div class="success-card">
            <div class="success-icon">
                <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>

            <h1>🎉 Order Confirmed!</h1>
            <p class="subtitle">
                Your order has been successfully placed. The seller has been notified and 
                will contact you shortly to arrange the pickup.
            </p>

            <div class="item-summary">
                <img src="<?php echo htmlspecialchars($order['ImageUrl'] ?: 'images/placeholder.png'); ?>" 
                     alt="<?php echo htmlspecialchars($order['item_name']); ?>" 
                     class="item-image"
                     onerror="this.src='images/placeholder.png'">
                <div class="item-info">
                    <h3><?php echo htmlspecialchars($order['item_name']); ?></h3>
                    <div class="item-price">Ksh. <?php echo number_format($order['Price'], 2); ?></div>
                    <div class="item-meta">
                        Condition: <strong><?php echo htmlspecialchars($order['item_condition'] ?? 'Used'); ?></strong>
                    </div>
                    <div class="item-meta">
                        Category: <strong><?php echo htmlspecialchars($order['item_category'] ?? 'General'); ?></strong>
                    </div>
                </div>
            </div>

            <div class="order-details">
                <h2> Order Details</h2>
                <div class="detail-row">
                    <span class="detail-label">Order ID</span>
                    <span class="detail-value">#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Order Date</span>
                    <span class="detail-value"><?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Pickup Location</span>
                    <span class="detail-value">Strathmore University Campus</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Preferred Pickup Time</span>
                    <span class="detail-value"><?php echo date('F j, Y g:i A', strtotime($order['pickup_time'])); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span class="detail-value">
                        <span class="status-badge"><?php echo ucfirst($order['order_status']); ?></span>
                    </span>
                </div>
                <?php if (!empty($order['notes'])): ?>
                <div class="detail-row">
                    <span class="detail-label">Your Notes</span>
                    <span class="detail-value"><?php echo htmlspecialchars($order['notes']); ?></span>
                </div>
                <?php endif; ?>
            </div>

            <div class="seller-info">
                <h3> Seller Contact Information</h3>
                <div class="contact-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <span><strong><?php echo htmlspecialchars($order['username']); ?></strong></span>
                </div>
                <div class="contact-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                    <a href="mailto:<?php echo htmlspecialchars($order['email']); ?>">
                        <?php echo htmlspecialchars($order['email']); ?>
                    </a>
                </div>
                <div class="contact-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                    <span><?php echo htmlspecialchars($order['buyer_phone']); ?></span>
                </div>
            </div>

            <div class="next-steps">
                <h3> Next Steps</h3>
                <ol>
                    <li>The seller will contact you via email to confirm pickup details</li>
                    <li>Coordinate a mutually convenient time and exact meeting location on campus</li>
                    <li>Meet at the agreed location to inspect and collect the item</li>
                    <li>Complete the transaction and enjoy your purchase!</li>
                    <li>Consider leaving feedback about your experience</li>
                </ol>
            </div>

            <div class="action-buttons">
                <a href="home.php" class="btn btn-primary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    Continue Shopping
                </a>
                <a href="my_listings.php" class="btn btn-secondary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                    </svg>
                    My Listings
                </a>
            </div>
        </div>
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

        // // Confetti animation (optional)
        // function createConfetti() {
        //     const colors = ['#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8'];
        //     for (let i = 0; i < 50; i++) {
        //         setTimeout(() => {
        //             const confetti = document.createElement('div');
        //             confetti.style.position = 'fixed';
        //             confetti.style.width = '10px';
        //             confetti.style.height = '10px';
        //             confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        //             confetti.style.left = Math.random() * window.innerWidth + 'px';
        //             confetti.style.top = '-10px';
        //             confetti.style.opacity = '1';
        //             confetti.style.borderRadius = '50%';
        //             confetti.style.pointerEvents = 'none';
        //             confetti.style.zIndex = '9999';
        //             document.body.appendChild(confetti);
                    
        //             let pos = -10;
        //             const fall = setInterval(() => {
        //                 if (pos >= window.innerHeight) {
        //                     clearInterval(fall);
        //                     confetti.remove();
        //                 } else {
        //                     pos += 5;
        //                     confetti.style.top = pos + 'px';
        //                     confetti.style.opacity = (window.innerHeight - pos) / window.innerHeight;
        //                 }
        //             }, 20);
        //         }, i * 30);
        //     }
        // }

        // // Trigger confetti on load
        // window.addEventListener('load', createConfetti);
    </script>
</body>
</html>