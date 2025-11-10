<?php
session_start();
require_once __DIR__ . '/../ClassAutoLoad.php';
require_once __DIR__ . '/../DBConnection.php';


if (!isset($_SESSION['pending_reset_user_id'])) {
    header("Location: /IAP-GROUP-PROJECT/index.php?form=login&error=UnauthorizedReset");
    exit();
}

$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$user_id = $_SESSION['pending_reset_user_id'];


if (empty($new_password) || $new_password !== $confirm_password) {
    
    header("Location: /IAP-GROUP-PROJECT/index.php?form=newpassword&error=PasswordsDoNotMatch");
    exit();
}
$db = new database($conf);
$conn = $db->connect();

try {
 
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    
    $db->query("UPDATE users SET password = ? WHERE id = ?",
        [$hashed_password, $user_id]);

    
    unset($_SESSION['pending_reset_user_id']);

   
    header("Location: /IAP-GROUP-PROJECT/index.php?form=login&message=PasswordResetSuccess");
    exit();

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}