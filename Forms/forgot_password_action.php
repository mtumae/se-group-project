<?php
session_start();
require_once __DIR__ . '/../ClassAutoLoad.php';
require_once __DIR__ . '/../DBConnection.php';

$email = trim($_POST['email'] ?? '');

if (empty($email)) {
    die("Email is required.");
}

$db = new database($conf);
$conn = $db->connect();

try {
   
    $stmt = $db->query("SELECT id FROM users WHERE email = ?", [$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
       
        $code = rand(100000, 999999);
        $expiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));

       
        $db->query("UPDATE users SET reset_code = ?, reset_code_expiry = ? WHERE id = ?",
            [$code, $expiry, $user['id']]);

        
        $mail = new Mail();
        $mail->sendPasswordResetCode($email, $code);


        $_SESSION['reset_pending_email'] = $email;

        
        header("Location: /IAP-GROUP-PROJECT/index.php?form=resetcode");
        exit();
    } else {
       
        header("Location: /IAP-GROUP-PROJECT/index.php?form=forgot_password&error=EmailNotFound");
        exit();
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}