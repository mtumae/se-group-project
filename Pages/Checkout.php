<?php
session_start();
require_once __DIR__ . '/../ClassAutoLoad.php';
require_once __DIR__ . '/../DBConnection.php';

$components = new Components();
$db = new Database($conf);
$db->connect();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /iap-group-project/index.php?form=login&redirect=' . urlencode('checkout.php?id=' . ($_GET['id'] ?? '')));
    exit;
}

$itemId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($itemId === 0) {
    header('Location: index.php');
    exit;
}

// Fetch item details with seller info - ADJUSTED TO YOUR TABLE STRUCTURE
$item = $db->fetch("SELECT items.*, users.username, users.email 
                    FROM items 
                    LEFT JOIN users ON items.user_id = users.id 
                    WHERE items.id = ?", [$itemId]);

if (!$item || count($item) === 0) {
    header('Location: index.php');
    exit;
}

$item = $item[0];

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $buyerName = trim($_POST['buyer_name'] ?? '');
    $buyerEmail = trim($_POST['buyer_email'] ?? '');
    $buyerPhone = trim($_POST['buyer_phone'] ?? '');
    $pickupTime = $_POST['pickup_time'] ?? '';
    $notes = trim($_POST['notes'] ?? '');
    
    // Validate inputs
    if (empty($buyerName) || empty($buyerEmail) || empty($buyerPhone) || empty($pickupTime)) {
        $error_message = "Please fill in all required fields.";
    } else {
        // Insert order into database
        $sql = "INSERT INTO orders (item_id, buyer_id, buyer_name, buyer_email, buyer_phone, pickup_time, notes, order_status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
        
        $result = $db->insert($sql, [
            $itemId, 
            $_SESSION['user_id'], 
            $buyerName, 
            $buyerEmail, 
            $buyerPhone, 
            $pickupTime, 
            $notes
        ]);
        
        if ($result) {
            $_SESSION['order_success'] = true;
            $_SESSION['last_order_id'] = $result; // Store the insert ID
            header('Location: order-confirmation.php?order_id=' . $result);
            exit;
        } else {
            $error_message = "Failed to place order. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - <?php echo htmlspecialchars($item['item_name']); ?></title>
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
        /* .theme-toggle {
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
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        h1 {
            margin-bottom: 30px;
            font-size: 2.5rem;
            color: var(--text-primary);
            font-weight: 700;
        }

        .checkout-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }

        .section {
            background: var(--bg-primary);
            padding: 35px;
            border-radius: 16px;
            box-shadow: 0 4px 20px var(--card-shadow);
            transition: all var(--transition);
        }

        .section h2 {
            font-size: 1.6rem;
            margin-bottom: 25px;
            color: var(--text-primary);
            padding-bottom: 15px;
            border-bottom: 2px solid var(--border-color);
            font-weight: 700;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--text-primary);
            font-size: 0.95rem;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--border-color);
            border-radius: 10px;
            font-size: 1rem;
            transition: all var(--transition);
            background: var(--bg-tertiary);
            color: var(--text-primary);
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
            font-family: inherit;
        }

        .required {
            color: #dc3545;
        }

        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            align-items: start;
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

        .alert-info {
            background: linear-gradient(135deg, #d1ecf1, #bee5eb);
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .alert-error {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .item-summary {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
            padding-bottom: 25px;
            border-bottom: 2px solid var(--border-color);
        }

        .summary-image {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 4px 12px var(--card-shadow);
        }

        .summary-details h3 {
            font-size: 1.3rem;
            margin-bottom: 12px;
            color: var(--text-primary);
            font-weight: 700;
        }

        .summary-price {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--accent-color), var(--accent-hover));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }

        .summary-meta {
            font-size: 0.9rem;
            color: var(--text-secondary);
            margin-bottom: 5px;
        }

        .pickup-info {
            background: linear-gradient(135deg, var(--bg-tertiary), var(--bg-secondary));
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            border: 1px solid var(--border-color);
        }

        .pickup-info h4 {
            font-size: 1.2rem;
            margin-bottom: 18px;
            color: var(--text-primary);
            font-weight: 700;
        }

        .info-item {
            display: flex;
            align-items: start;
            margin-bottom: 15px;
            padding: 12px;
            background: var(--bg-primary);
            border-radius: 8px;
            transition: all var(--transition);
        }

        .info-item:hover {
            transform: translateX(5px);
        }

        .info-item svg {
            margin-right: 12px;
            color: var(--accent-color);
            flex-shrink: 0;
            margin-top: 2px;
        }

        .info-text {
            font-size: 0.95rem;
            line-height: 1.6;
            color: var(--text-secondary);
        }

        .price-breakdown {
            margin-top: 25px;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 15px;
            background: var(--bg-tertiary);
            border-radius: 8px;
            transition: all var(--transition);
        }

        .price-row:hover {
            background: var(--bg-secondary);
        }

        .price-row:last-child {
            background: linear-gradient(135deg, var(--accent-color), var(--accent-hover));
            color: white;
            font-size: 1.4rem;
            font-weight: 800;
            margin-top: 10px;
        }

        .price-row:last-child .price-label,
        .price-row:last-child .price-value {
            color: white;
        }

        .price-label {
            color: var(--text-tertiary);
            font-weight: 500;
        }

        .price-value {
            font-weight: 700;
            color: var(--text-primary);
        }

        .btn {
            width: 100%;
            padding: 18px;
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
            color: var(--text-secondary);
            border: 2px solid var(--border-color);
            margin-top: 15px;
        }

        .btn-secondary:hover {
            background: var(--bg-tertiary);
            border-color: var(--text-tertiary);
        }

        .seller-contact {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            padding: 25px;
            border-radius: 12px;
            margin-top: 20px;
            border: 1px solid #ffeaa7;
        }

        .seller-contact h4 {
            font-size: 1.1rem;
            margin-bottom: 12px;
            color: #856404;
            font-weight: 700;
        }

        .seller-contact p {
            font-size: 0.95rem;
            color: #856404;
            line-height: 1.7;
        }

        @media (max-width: 968px) {
            .theme-toggle {
                top: 80px;
                right: 15px;
                padding: 8px 16px;
            }

            .checkout-grid {
                grid-template-columns: 1fr;
            }

            .item-summary {
                flex-direction: column;
            }

            .summary-image {
                width: 100%;
                height: 200px;
            }

            h1 {
                font-size: 2rem;
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
        <h1> Checkout</h1>

        <?php if (isset($error_message)): ?>
        <div class="alert alert-error">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="15" y1="9" x2="9" y2="15"></line>
                <line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
            <span><?php echo htmlspecialchars($error_message); ?></span>
        </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="checkout-grid">
                <div>
                    <div class="section">
                        <h2> Contact Information</h2>
                        
                        <div class="alert alert-info">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                            <span>Please provide accurate contact information so the seller can coordinate pickup with you.</span>
                        </div>

                        <div class="form-group">
                            <label for="buyer_name">Full Name <span class="required">*</span></label>
                            <input type="text" id="buyer_name" name="buyer_name" required placeholder="Enter your full name">
                        </div>

                        <div class="form-group">
                            <label for="buyer_email">Email Address <span class="required">*</span></label>
                            <input type="email" id="buyer_email" name="buyer_email" required placeholder="your.email@example.com">
                        </div>

                        <div class="form-group">
                            <label for="buyer_phone">Phone Number <span class="required">*</span></label>
                            <input type="tel" id="buyer_phone" name="buyer_phone" required placeholder="+254 700 000 000">
                        </div>
                    </div>

                    <div class="section" style="margin-top: 30px;">
                        <h2>📍 Pickup Details</h2>

                        <div class="form-group">
                            <label for="pickup_time">Preferred Pickup Date & Time <span class="required">*</span></label>
                            <input type="datetime-local" id="pickup_time" name="pickup_time" required>
                        </div>

                        <div class="form-group">
                            <label for="notes">Additional Notes (Optional)</label>
                            <textarea id="notes" name="notes" placeholder="Any special instructions or questions for the seller..."></textarea>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="section">
                        <h2>Order Summary</h2>

                        <div class="item-summary">
                            <img src="<?php echo htmlspecialchars($item['ImageUrl'] ?: 'images/placeholder.png'); ?>" 
                                 alt="<?php echo htmlspecialchars($item['item_name']); ?>" 
                                 class="summary-image"
                                 onerror="this.src='images/placeholder.png'">
                            <div class="summary-details">
                                <h3><?php echo htmlspecialchars($item['item_name']); ?></h3>
                                <div class="summary-price">Ksh. <?php echo number_format($item['Price'], 2); ?></div>
                                <div class="summary-meta">
                                    <strong>Condition:</strong> <?php echo htmlspecialchars($item['item_condition'] ?? 'Used'); ?>
                                </div>
                                <div class="summary-meta">
                                    <strong>Category:</strong> <?php echo htmlspecialchars($item['item_category'] ?? 'General'); ?>
                                </div>
                                <div class="summary-meta">
                                    <strong>Quantity:</strong> <?php echo htmlspecialchars($item['quantity'] ?? '1'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="pickup-info">
                            <h4>Pickup Information</h4>
                            <div class="info-item">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                <div class="info-text">
                                    <strong>Location:</strong><br>
                                    Strathmore University Campus
                                </div>
                            </div>
                            <div class="info-item">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                <div class="info-text">
                                    <strong>Availability:</strong><br>
                                    Coordinate with seller after checkout
                                </div>
                            </div>
                        </div>

                        <div class="price-breakdown">
                            <div class="price-row">
                                <span class="price-label">Item Price</span>
                                <span class="price-value">Ksh. <?php echo number_format($item['Price'], 2); ?></span>
                            </div>
                            <div class="price-row">
                                <span class="price-label">Service Fee</span>
                                <span class="price-value">Ksh. 0.00</span>
                            </div>
                            <div class="price-row">
                                <span class="price-label">Total Amount</span>
                                <span class="price-value">Ksh. <?php echo number_format($item['Price'], 2); ?></span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Confirm Order
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="history.back()">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="19" y1="12" x2="5" y2="12"></line>
                                <polyline points="12 19 5 12 12 5"></polyline>
                            </svg>
                            Go Back
                        </button>

                        <div class="seller-contact">
                            <h4>Seller Contact</h4>
                            <p>
                                After confirming your order, you'll receive the seller's contact information 
                                (<strong><?php echo htmlspecialchars($item['username']); ?></strong>) to arrange the pickup details.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
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

        // Set minimum datetime to current time
        document.addEventListener('DOMContentLoaded', function() {
            const pickupInput = document.getElementById('pickup_time');
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            pickupInput.min = now.toISOString().slice(0, 16);
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const phone = document.getElementById('buyer_phone').value;
            const phoneRegex = /^[+]?[\d\s-()]+$/;
            
            if (!phoneRegex.test(phone)) {
                e.preventDefault();
                alert('Please enter a valid phone number');
                return false;
            }
        });
    </script>
</body>
</html>