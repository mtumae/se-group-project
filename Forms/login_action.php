<?php
session_start();
require_once __DIR__ . '/../ClassAutoLoad.php';
require_once __DIR__ . '/../DBConnection.php';


$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    // Redirect back to login with an error
    header("Location: /IAP-GROUP-PROJECT/index.php?form=login&error=empty");
    exit();
}

if (!isset($conf)) {
    die("Config file not loaded. Check ClassAutoLoad.php.");
}
$db = new database($conf);
$conn = $db->connect();

try {
    
    // --- MODIFIED QUERY ---
    // We now select the role_id from the users table
    $sql = "SELECT id, username, password, role_id FROM users WHERE email = ?";
            
    $stmt = $db->query($sql, [$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the user data

    if ($user) {
        // First, verify the password
        if (password_verify($password, $user['password'])) {
            
            // --- NEW ROLE CHECK ---
            // Password is correct, now check the role_id
            // 1 = admin, 2 = user (or anything else)
            if ($user['role_id'] == 1) {
                // USER IS AN ADMIN
                session_regenerate_id(true); // Security best practice
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role_id'] = $user['role_id']; // Store role_id
                
                // Redirect to the new admin dashboard
                header("Location: /IAP-GROUP-PROJECT/Pages/admin_dashboard.php");
                exit();

            } else {
                // USER IS A NORMAL USER
                // Proceed with your existing 2FA logic
                $code = rand(100000, 999999);
                $_SESSION['pending_user_id'] = $user['id'];
                $_SESSION['pending_username'] = $user['username'];
                $_SESSION['pending_email'] = $email;
                $_SESSION['2fa_code'] = $code;

                $mail = new Mail();
                 $mail->verifyAccount($email, $code);

                header("Location: /IAP-GROUP-PROJECT/index.php?form=twofa");
                exit();
            }

        } else {
            // Invalid password
            header("Location: /IAP-GROUP-PROJECT/index.php?form=login&error=invalid");
            exit();
        }
    } else {
        // No account found with that email
        header("Location: /IAP-GROUP-PROJECT/index.php?form=login&error=notfound");
        exit();
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>