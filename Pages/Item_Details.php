<?php
require_once __DIR__ . '/../ClassAutoLoad.php';
require_once __DIR__ . '/../DBConnection.php';

$components = new Components();
$db = new Database($conf);
$db->connect();


$itemId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($itemId === 0) {
    header('Location: index.php');
    exit;
}

$item = $db->fetch("SELECT items.*, users.username, users.email
                    FROM items 
                    LEFT JOIN users ON items.user_id = users.id 
                    WHERE items.id = ?", [$itemId]);

if (!$item || count($item) === 0) {
    header('Location: index.php');
    exit;
}

$item = $item[0];


$relatedItems = $db->fetch("SELECT * FROM items 
                            WHERE item_category = ? AND id != ? 
                            LIMIT 4", 
                            [$item['item_category'], $itemId]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($item['item_name']); ?> - StrathMart</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .breadcrumb {
            margin-bottom: 20px;
            font-size: 0.9rem;
            color: #666;
        }

        .breadcrumb a {
            color: #007bff;
            text-decoration: none;
            transition: color 0.3s;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
            color: #0056b3;
        }

        .breadcrumb span {
            margin: 0 8px;
        }

        .item-details-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 60px;
        }

        /* Image Section */
        .image-section {
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 20px;
            height: fit-content;
        }

        .main-image-container {
            width: 100%;
            height: 500px;
            border-radius: 12px;
            overflow: hidden;
            background-color: #f8f9fa;
            margin-bottom: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .main-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .image-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background-color: #007bff;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
        }

        /* Details Section */
        .details-section {
            display: flex;
            flex-direction: column;
        }

        .item-header {
            margin-bottom: 25px;
        }

        .details-section h1 {
            font-size: 2.2rem;
            margin-bottom: 15px;
            color: #1a1a1a;
            line-height: 1.3;
        }

        .price-section {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
        }

        .price {
            font-size: 2.8rem;
            font-weight: 700;
            color: #007bff;
        }

        .item-meta {
            display: flex;
            gap: 25px;
            flex-wrap: wrap;
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
        }

        .meta-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .meta-label {
            font-size: 0.8rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .meta-value {
            font-size: 1rem;
            font-weight: 600;
            color: #333;
        }

        .condition-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .condition-new {
            background-color: #d4edda;
            color: #155724;
        }

        .condition-used {
            background-color: #fff3cd;
            color: #856404;
        }

        .condition-like-new {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        /* Description */
        .description-section {
            margin-bottom: 35px;
            padding-bottom: 35px;
            border-bottom: 1px solid #e9ecef;
        }

        .description-section h2 {
            font-size: 1.4rem;
            margin-bottom: 15px;
            color: #1a1a1a;
            font-weight: 600;
        }

        .description-text {
            line-height: 1.8;
            color: #555;
            font-size: 1rem;
        }

        /* Pickup Information */
        .pickup-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 30px;
        }

        .pickup-section h3 {
            font-size: 1.3rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .pickup-item {
            display: flex;
            align-items: start;
            margin-bottom: 15px;
            padding: 12px;
            background-color: rgba(255, 255, 255, 0.15);
            border-radius: 8px;
        }

        .pickup-item:last-child {
            margin-bottom: 0;
        }

        .pickup-item svg {
            margin-right: 12px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .pickup-text {
            flex: 1;
        }

        .pickup-label {
            font-size: 0.85rem;
            opacity: 0.9;
            margin-bottom: 5px;
        }

        .pickup-value {
            font-size: 1rem;
            font-weight: 600;
        }

        /* Seller Info */
        .seller-info {
            background-color: #f8f9fa;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 30px;
        }

        .seller-info h3 {
            font-size: 1.3rem;
            margin-bottom: 20px;
            color: #1a1a1a;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .seller-detail {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            padding: 10px;
            background-color: white;
            border-radius: 8px;
            transition: transform 0.2s;
        }

        .seller-detail:hover {
            transform: translateX(5px);
        }

        .seller-detail svg {
            margin-right: 12px;
            color: #007bff;
        }

        .seller-detail a {
            color: #007bff;
            text-decoration: none;
        }

        .seller-detail a:hover {
            text-decoration: underline;
        }

       
        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: auto;
        }

        .btn {
            padding: 16px 30px;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
        }

        .btn-secondary {
            background-color: white;
            color: #007bff;
            border: 2px solid #007bff;
        }

        .btn-secondary:hover {
            background-color: #007bff;
            color: white;
        }

        .btn-outline {
            background-color: transparent;
            color: #6c757d;
            border: 2px solid #dee2e6;
        }

        .btn-outline:hover {
            background-color: #f8f9fa;
            border-color: #adb5bd;
        }

        
        .related-items {
            margin-top: 80px;
        }

        .related-items h2 {
            font-size: 2rem;
            margin-bottom: 40px;
            text-align: center;
            color: #1a1a1a;
        }

        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 30px;
        }

        .related-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
        }

        .related-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .related-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .related-details {
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .related-name {
            font-weight: 600;
            font-size: 1.05rem;
            color: #1a1a1a;
            line-height: 1.4;
        }

        .related-price {
            color: #007bff;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .related-meta {
            font-size: 0.85rem;
            color: #666;
        }

      
        @media (max-width: 968px) {
            .item-details-container {
                grid-template-columns: 1fr;
                padding: 30px 20px;
            }

            .image-section {
                position: static;
            }

            .main-image-container {
                height: 400px;
            }

            .details-section h1 {
                font-size: 1.8rem;
            }

            .price {
                font-size: 2.2rem;
            }

            .item-meta {
                flex-direction: column;
                gap: 15px;
            }

            .related-grid {
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
                gap: 20px;
            }
        }

        
        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
    </style>
</head>
<body>
    <?php $components->header(); ?>

    <div class="container">
       

        <div class="item-details-container">
         
            <div class="image-section">
                <div class="main-image-container">
                    <?php if (!empty($item['item_condition']) && $item['item_condition'] === 'New'): ?>
                    <span class="image-badge"> Brand New</span>
                    <?php endif; ?>
                    <img src="<?php echo htmlspecialchars($item['ImageUrl'] ?: 'images/placeholder.png'); ?>" 
                         alt="<?php echo htmlspecialchars($item['item_name']); ?>" 
                         class="main-image"
                         id="mainImage">
                </div>
            </div>

           
            <div class="details-section">
                <div class="item-header">
                    <h1><?php echo htmlspecialchars($item['item_name']); ?></h1>
                    
                    <div class="price-section">
                        <div class="price">Ksh. <?php echo number_format($item['Price'], 2); ?></div>
                    </div>
                </div>

                <div class="item-meta">
                    <div class="meta-item">
                        <span class="meta-label">Condition</span>
                        <span class="meta-value">
                            <span class="condition-badge condition-<?php echo strtolower(str_replace(' ', '-', $item['item_condition'] ?? 'used')); ?>">
                                <?php echo htmlspecialchars($item['item_condition'] ?? 'Used'); ?>
                            </span>
                        </span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Category</span>
                        <span class="meta-value"><?php echo htmlspecialchars($item['item_category'] ?? 'General'); ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Posted</span>
                        <span class="meta-value">
                            <?php 
                            if (!empty($item['created_at'])) {
                                $date = new DateTime($item['created_at']);
                                $now = new DateTime();
                                $diff = $now->diff($date);
                                
                                if ($diff->days == 0) {
                                    echo "Today";
                                } elseif ($diff->days == 1) {
                                    echo "Yesterday";
                                } elseif ($diff->days < 7) {
                                    echo $diff->days . " days ago";
                                } else {
                                    echo $date->format('M j, Y');
                                }
                            } else {
                                echo "Recently";
                            }
                            ?>
                        </span>
                    </div>
                </div>

                <div class="description-section">
                    <h2> Description</h2>
                    <p class="description-text"><?php echo nl2br(htmlspecialchars($item['item_description'])); ?></p>
                </div>

                <div class="pickup-section">
                    <h3>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        Pickup Details
                    </h3>
                    <div class="pickup-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        <div class="pickup-text">
                            <div class="pickup-label">Location</div>
                            <div class="pickup-value"><?php echo htmlspecialchars($item['pickup_location'] ?? 'Strathmore University Campus'); ?></div>
                        </div>
                    </div>
                    <div class="pickup-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <div class="pickup-text">
                            <div class="pickup-label">Availability</div>
                            <div class="pickup-value"><?php echo htmlspecialchars($item['availability'] ?? 'Flexible - coordinate with seller'); ?></div>
                        </div>
                    </div>
                </div>

                <div class="seller-info">
                    <h3>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        Seller Information
                    </h3>
                    <div class="seller-detail">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span><?php echo htmlspecialchars($item['username'] ?? 'Stratizen'); ?></span>
                    </div>
                    <div class="seller-detail">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        <a href="mailto:<?php echo htmlspecialchars($item['email'] ?? 'contact@strathmart.com'); ?>">
                            <?php echo htmlspecialchars($item['email'] ?? 'contact@strathmart.com'); ?>
                        </a>
                    </div>
                    <?php if (!empty($item['phone'])): ?>
                    <div class="seller-detail">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                        <a href="tel:<?php echo htmlspecialchars($item['phone']); ?>">
                            <?php echo htmlspecialchars($item['phone']); ?>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="action-buttons">
                    <!-- In item-details.php, replace action buttons section -->
<div class="action-buttons">
    <a href="cart.php?add=1&id=<?php echo $item['id']; ?>" class="btn btn-primary">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
        </svg>
        Add to Cart
    </a>
    
    <a href="checkout.php?id=<?php echo $item['id']; ?>" class="btn btn-secondary">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="16"></line>
            <line x1="8" y1="12" x2="16" y2="12"></line>
        </svg>
        Buy Now
    </a>
    
    <button class="btn btn-secondary" onclick="contactSeller()">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
        Message Seller
    </button>
    
    <button class="btn btn-outline" onclick="history.back()">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        Back to Listings
    </button>
</div>
                    
                </div>
            </div>
        </div>

        <?php if ($relatedItems && count($relatedItems) > 0): ?>
        <div class="related-items">
            <h2>You May Also Like</h2>
            <div class="related-grid">
                <?php foreach ($relatedItems as $related): ?>
                <a href="item_details.php?id=<?php echo $related['id']; ?>" class="related-card">
                    <img src="<?php echo htmlspecialchars($related['ImageUrl'] ?: 'images/placeholder.png'); ?>" 
                         alt="<?php echo htmlspecialchars($related['item_name']); ?>" 
                         class="related-image">
                    <div class="related-details">
                        <div class="related-name"><?php echo htmlspecialchars($related['item_name']); ?></div>
                        <div class="related-price">Ksh. <?php echo number_format($related['Price'], 2); ?></div>
                        <div class="related-meta"><?php echo htmlspecialchars($related['item_condition'] ?? 'Used'); ?></div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script>
        function contactSeller() {
            const email = '<?php echo htmlspecialchars($item['email'] ?? ''); ?>';
            const itemName = '<?php echo htmlspecialchars($item['item_name']); ?>';
            const subject = `Interested in: ${itemName}`;
            const body = `Hi,\n\nI'm interested in your item "${itemName}" listed on StrathMart for Ksh. <?php echo number_format($item['Price'], 2); ?>.\n\nI would like to know more about it.\n\nThank you!`;
            
            window.location.href = `mailto:${email}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
        }

       
        document.getElementById('mainImage')?.addEventListener('click', function() {
            this.style.transform = this.style.transform === 'scale(1.5)' ? 'scale(1)' : 'scale(1.5)';
            this.style.cursor = this.style.transform === 'scale(1.5)' ? 'zoom-out' : 'zoom-in';
            this.style.transition = 'transform 0.3s ease';
        });
    </script>
</body>
</html>