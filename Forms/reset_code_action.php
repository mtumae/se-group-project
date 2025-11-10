<?php
session_start();
require_once __DIR__ . '/../ClassAutoLoad.php';
require_once __DIR__ . '/../DBConnection.php';

$entered_code = trim($_POST['reset_code'] ?? '');
$current_time = date('Y-m-d H:i:s');

if (!preg_match('/^\d{6}$/', $entered_code)) {
  
    header("Location: /IAP-GROUP-PROJECT/index.php?form=resetcode&error=InvalidCodeFormat");
    exit();
}

$db = new database($conf);
$conn = $db->connect();

try {
   
    $stmt = $db->query("SELECT id, email FROM users WHERE reset_code = ? AND reset_code_expiry > ?",
        [$entered_code, $current_time]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        
        $db->query("UPDATE users SET reset_code = NULL, reset_code_expiry = NULL WHERE id = ?",
            [$user['id']]);

        
        $_SESSION['pending_reset_user_id'] = $user['id'];
        unset($_SESSION['reset_pending_email']); 

      
        header("Location: /IAP-GROUP-PROJECT/index.php?form=newpassword");
        exit();

   

} else {
  
    header("Location: /IAP-GROUP-PROJECT/index.php?form=resetcode&error=InvalidOrExpiredCode");
    exit();
}

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}