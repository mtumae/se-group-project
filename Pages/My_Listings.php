<?php
session_start();
require_once __DIR__ . '/../ClassAutoLoad.php';
require_once __DIR__ . '/../DBConnection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /SE-GROUP-PROJECT/index.php?form=login");
    exit();
}

$components = new Components();
$db = new Database($conf);
$db->connect();

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_item'])) {
    $item_name = trim($_POST['item_name']);
    $item_description = trim($_POST['item_description']);
    $quantity = intval($_POST['quantity']);
    $category_name = trim($_POST['item_category']); 
    $price = floatval($_POST['item_price']);        
    $condition = trim($_POST['item_condition']);    
    $imageUrl = '';

    
    if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        $file_type = $_FILES['item_image']['type'];

        if (in_array($file_type, $allowed_types)) {
            $upload_dir = 'images/items/';

            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_extension = pathinfo($_FILES['item_image']['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid('item_') . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['item_image']['tmp_name'], $upload_path)) {
                $imageUrl = $upload_path;
            } else {
                $error_message = "Failed to upload image.";
            }
        } else {
            $error_message = "Invalid file type. Please upload JPG, PNG, or GIF.";
        }
    }

    if (empty($error_message)) {
      
        $sql = "INSERT INTO items 
                (user_id, item_name, quantity, item_description, item_category, Price, ImageUrl, item_condition, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $result = $db->insert($sql, [
            $user_id,
            $item_name,
            $quantity,
            $item_description,
            $category_name,
            $price,
            $imageUrl,
            $condition
        ]);

        if ($result) {
            $success_message = "Item added successfully!";
        } else {
            $error_message = "Failed to add item. Please check your database connection.";
        }
    }
}

if (isset($_GET['delete_item'])) {
    $item_id = intval($_GET['delete_item']);

    $item = $db->fetch("SELECT * FROM items WHERE id = ? AND user_id = ?", [$item_id, $user_id]);

    if ($item && count($item) > 0) {
        if (!empty($item[0]['ImageUrl']) && file_exists($item[0]['ImageUrl'])) {
            unlink($item[0]['ImageUrl']);
        }

        $db->delete("DELETE FROM items WHERE id = ?", [$item_id]);
        $success_message = "Item deleted successfully!";
    }
}


$user_items = $db->fetch("SELECT *, Price as item_price, ImageUrl as item_image FROM items WHERE user_id = ? ORDER BY created_at DESC", [$user_id]);?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="sell-items.css">
    <title>My Listings - StrathMart</title>
    <style>
        * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f8f9fa;
    color: #333;
}

.sell-page-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 30px 20px;
}
.logout-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background-color: #dc3545;
    color: #fff;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.95rem;
    transition: background-color 0.3s, transform 0.2s, box-shadow 0.2s;
    box-shadow: 0 2px 6px rgba(220, 53, 69, 0.2);
}

.logout-btn:hover {
    background-color: #c82333;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);
}

.logout-btn:active {
    transform: translateY(0);
    box-shadow: 0 1px 4px rgba(220, 53, 69, 0.2);
}

/* Page Header */
.page-header {
    display: flex;
    justify-content: space-between;
    text-align: center;
    margin-bottom: 40px;
}

.page-header h1 {
    font-size: 2.5rem;
    color: #333;
    margin-bottom: 10px;
}

.page-header p {
    font-size: 1.1rem;
    color: #666;
}

/* Alerts */
.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    font-weight: 500;
    transition: opacity 0.3s;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* Content Wrapper */
.content-wrapper {
    display: grid;
    grid-template-columns: 1fr 1.5fr;
    gap: 30px;
}

/* Add Item Section */
.add-item-section {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    height: fit-content;
    position: sticky;
    top: 20px;
}

.add-item-section h2,
.my-items-section h2 {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.5rem;
    margin-bottom: 25px;
    color: #333;
}

.add-item-section h2 svg,
.my-items-section h2 svg {
    color: #007bff;
}

/* Form Styles */
.add-item-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-weight: 600;
    margin-bottom: 8px;
    color: #333;
    font-size: 0.95rem;
}

.form-group input[type="text"],
.form-group input[type="number"],
.form-group select,
.form-group textarea {
    padding: 12px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 1rem;
    font-family: inherit;
    transition: border-color 0.3s;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #007bff;
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
}

.form-group small {
    margin-top: 5px;
    color: #666;
    font-size: 0.85rem;
}

/* File Upload */
.file-upload-wrapper {
    position: relative;
}

.form-group input[type="file"] {
    padding: 10px;
    border: 2px dashed #007bff;
    border-radius: 8px;
    cursor: pointer;
    background-color: #f8f9fa;
}

.file-upload-preview {
    display: none;
    margin-top: 15px;
    border-radius: 8px;
    overflow: hidden;
    max-width: 100%;
}

.file-upload-preview img {
    width: 100%;
    height: auto;
    max-height: 300px;
    object-fit: contain;
}

/* Buttons */
.btn-primary {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 14px 28px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.2s;
}

.btn-primary:hover {
    background-color: #0056b3;
    transform: translateY(-2px);
}

.btn-primary:active {
    transform: translateY(0);
}

/* My Items Section */
.my-items-section {
    min-height: 400px;
}

.items-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 25px;
}

/* User Item Card */
.user-item-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}

.user-item-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.user-item-card .item-image-container {
    position: relative;
    width: 100%;
    height: 200px;
    overflow: hidden;
    background-color: #e0e0e0;
}

.user-item-card .item-image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.item-condition-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: rgba(0, 123, 255, 0.9);
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.item-info {
    padding: 20px;
}

.item-info h3 {
    font-size: 1.2rem;
    margin-bottom: 8px;
    color: #333;
}

.item-category {
    display: inline-block;
    background-color: #f0f0f0;
    color: #666;
    padding: 4px 10px;
    border-radius: 15px;
    font-size: 0.85rem;
    margin-bottom: 10px;
}

.item-info .item-price {
    font-size: 1.4rem;
    font-weight: 700;
    color: #007bff;
    margin-bottom: 10px;
}

.item-info .item-description {
    font-size: 0.9rem;
    color: #666;
    line-height: 1.5;
    margin-bottom: 15px;
}

/* Item Actions */
.item-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.btn-edit,
.btn-delete {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 10px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.9rem;
    transition: all 0.3s;
}

.btn-edit {
    background-color: #28a745;
    color: white;
}

.btn-edit:hover {
    background-color: #218838;
}

.btn-delete {
    background-color: #dc3545;
    color: white;
}

.btn-delete:hover {
    background-color: #c82333;
}

/* No Items Message */
.no-items {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
    color: #999;
}

.no-items svg {
    margin-bottom: 20px;
    opacity: 0.5;
}

.no-items p {
    font-size: 1.2rem;
    margin-bottom: 10px;
}

.no-items small {
    font-size: 1rem;
    color: #bbb;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .content-wrapper {
        grid-template-columns: 1fr;
    }
    
    .add-item-section {
        position: static;
    }
}

@media (max-width: 768px) {
    .page-header h1 {
        font-size: 2rem;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .items-grid {
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    }
}

@media (max-width: 480px) {
    .sell-page-container {
        padding: 20px 10px;
    }
    
    .add-item-section,
    .user-item-card {
        border-radius: 8px;
    }
    
    .item-actions {
        flex-direction: column;
    }
}

/* Logout Confirmation Modal */
.logout-modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.logout-modal-content {
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    width: 90%;
    max-width: 400px;
    text-align: center;
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    animation: fadeIn 0.3s ease;
}

.logout-modal-content h3 {
    margin-bottom: 10px;
    color: #333;
}

.logout-modal-content p {
    color: #555;
    margin-bottom: 20px;
}

.modal-actions {
    display: flex;
    justify-content: space-around;
    gap: 10px;
}

.btn-cancel,
.btn-logout {
    padding: 10px 20px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.2s;
}

.btn-cancel {
    background-color: #e0e0e0;
    color: #333;
}

.btn-cancel:hover {
    background-color: #ccc;
}

.btn-logout {
    background-color: #dc3545;
    color: white;
}

.btn-logout:hover {
    background-color: #c82333;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

    </style>
</head>
<body>
<?php $components->header(); ?>

<div class="sell-page-container">
    <div class="page-header">
        <h1>My Listings</h1>
        <p>Manage your items and add new listings</p>
        <a href="logout.php" class="logout-btn">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
        <polyline points="16 17 21 12 16 7"></polyline>
        <line x1="21" y1="12" x2="9" y2="12"></line>
    </svg>
    Log Out
    </a>

    </div>

    <?php if($success_message): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success_message); ?>
        </div>
    <?php endif; ?>

    <?php if($error_message): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <div class="content-wrapper">
        <!-- Add New Item Form -->
        <div class="add-item-section">
            <h2>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="16"></line>
                    <line x1="8" y1="12" x2="16" y2="12"></line>
                </svg>
                Add New Item
            </h2>
            
            <form method="POST" enctype="multipart/form-data" class="add-item-form">
                <div class="form-group">
                    <label for="item_name">Item Name *</label>
                    <input type="text" id="item_name" name="item_name" required maxlength="100">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="item_category">Category *</label>
                        <select id="item_category" name="item_category" required>
                        <option value="">Select Category</option>
                                <option value="Electronics">Electronics</option>
                                <option value="Footwear">Footwear</option>
                                <option value="Furniture">Furniture</option>
                                <option value="Appliances">Appliances</option>
                                <option value="Music">Music</option>
                                <option value="Home Decor">Home Decor</option>
                                <option value="Accessories">Accessories</option>
                                <option value="Clothing">Clothing</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="item_condition">Condition *</label>
                        <select id="item_condition" name="item_condition" required>
                            <option value="">Select Condition</option>
                            <option value="New">New</option>
                            <option value="Like New">Like New</option>
                            <option value="Good">Good</option>
                            <option value="Fair">Fair</option>
                            <option value="Used">Used</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="item_price">Price (Ksh) *</label>
                        <input type="number" id="item_price" name="item_price" step="0.01" min="0" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="quantity">Quantity *</label>
                    <input type="number" id="quantity" name="quantity" min="1" required>
                </div>

                <div class="form-group">
                    <label for="item_description">Description *</label>
                    <textarea id="item_description" name="item_description" rows="4" required maxlength="500"></textarea>
                    <small>Maximum 500 characters</small>
                </div>

                <div class="form-group">
                    <label for="item_image">Item Image *</label>
                    <div class="file-upload-wrapper">
                        <input type="file" id="item_image" name="item_image" accept="image/*" required>
                        <div class="file-upload-preview" id="imagePreview"></div>
                    </div>
                    <small>Supported formats: JPG, PNG, GIF (Max 5MB)</small>
                </div>


                <button type="submit" name="add_item" class="btn-primary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    Add Item
                </button>
            </form>
        </div>

        <!-- User's Listed Items -->
        <div class="my-items-section">
            <h2>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                </svg>
                My Items (<?php echo count($user_items); ?>)
            </h2>

            <div class="items-grid">
                <?php if(!$user_items || count($user_items) == 0): ?>
                    <div class="no-items">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <p>You haven't listed any items yet</p>
                        <small>Add your first item using the form above</small>
                    </div>
                <?php else: ?>
                    <?php foreach($user_items as $item): 
                        $itemImage = !empty($item['item_image']) ? $item['item_image'] : 'images/placeholder.png';
                        $itemPrice = number_format($item['item_price'], 2);
                    ?>
                        <div class="user-item-card">
                            <div class="item-image-container">
                                <img src="<?php echo htmlspecialchars($itemImage); ?>" alt="<?php echo htmlspecialchars($item['item_name']); ?>">
                                <span class="item-condition-badge"><?php echo htmlspecialchars($item['item_condition']); ?></span>
                            </div>
                            <div class="item-info">
                                <h3><?php echo htmlspecialchars($item['item_name']); ?></h3>
                                <p class="item-category"><?php echo htmlspecialchars($item['item_category']); ?></p>
                                <p class="item-price">Ksh. <?php echo $itemPrice; ?></p>
                                <p class="item-description"><?php echo htmlspecialchars(substr($item['item_description'], 0, 100)) . (strlen($item['item_description']) > 100 ? '...' : ''); ?></p>
                                <div class="item-actions">
                                    <a href="edit-item.php?id=<?php echo $item['id']; ?>" class="btn-edit">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                        Edit
                                    </a>
                                    <a href="?delete_item=<?php echo $item['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this item?')">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        </svg>
                                        Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Image preview functionality
document.getElementById('item_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('imagePreview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
            preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
});

// Auto-hide alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        alert.style.opacity = '0';
        setTimeout(function() {
            alert.style.display = 'none';
        }, 300);
    });
}, 5000);


document.querySelector('.logout-btn').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('logoutModal').style.display = 'flex';
});

document.getElementById('cancelLogout').addEventListener('click', function() {
    document.getElementById('logoutModal').style.display = 'none';
});

window.addEventListener('click', function(e) {
    if (e.target.id === 'logoutModal') {
        document.getElementById('logoutModal').style.display = 'none';
    }
});


</script>

<div id="logoutModal" class="logout-modal">
  <div class="logout-modal-content">
    <h3>Confirm Logout</h3>
    <p>Are you sure you want to log out?</p>
    <div class="modal-actions">
      <button id="cancelLogout" class="btn-cancel">Cancel</button>
      <a href="logout.php" class="btn-logout">Log Out</a>
    </div>
  </div>
</div>

</body>
</html>