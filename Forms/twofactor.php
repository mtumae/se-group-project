<?php
session_start();

if (!isset($_SESSION['2fa_code'], $_SESSION['pending_user_id'])) {
    header("Location: /SE-GROUP-PROJECT/index.php?form=login");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered = trim($_POST['verification_code'] ?? '');

    if (!preg_match('/^\d{6}$/', $entered)) {
        $error = "Invalid code format. Please enter a 6-digit number.";
    } elseif ($entered == $_SESSION['2fa_code']) {
        $_SESSION['user_id']   = $_SESSION['pending_user_id'];
        $_SESSION['username']  = $_SESSION['pending_username'];
        $_SESSION['email']     = $_SESSION['pending_email'];

        unset($_SESSION['2fa_code'], $_SESSION['pending_user_id'], $_SESSION['pending_username'], $_SESSION['pending_email']);

        // Redirect to dashboard
        //header("Location: /IAP_PROJECT/users.php");
        header("Location: /SE-GROUP-PROJECT/pages/my_listings.php");
        exit();
    } else {
        $error = "Invalid or expired verification code.";
    }
}
?>

<!-- <!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Two-Factor Authentication</title>
    <style>
        /* ===== Dashboard / Users.css styling ===== */
        body {
            background: #212529; 
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #fff;
            margin: 0;
            padding: 0;
        }

        #dashboard {
            width: 100%;
            margin: 0;
            padding: 32px;
            background: #23272b; 
            min-height: 100vh;   
            box-sizing: border-box;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .twofa-container {
            width: 100%;
            max-width: 400px;
            padding: 32px;
            background: #2c3034;
            border-radius: 12px;
            box-sizing: border-box;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }

        .twofa-container h2 {
            font-size: 2rem;
            margin-bottom: 24px;
            font-weight: 600;
            color: #fff;
            text-align: center;
        }

        .twofa-container form {
            display: flex;
            flex-direction: column;
        }

        .twofa-container input[type="text"] {
            padding: 12px 14px;
            margin-bottom: 16px;
            border-radius: 8px;
            border: 1px solid #444;
            background: #343a40;
            color: #fff;
            font-size: 1rem;
            outline: none;
        }

        .twofa-container input[type="text"]::placeholder {
            color: #ccc;
        }

        .twofa-container button {
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: #007bff;
            color: #fff;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .twofa-container button:hover {
            background: #0056b3;
        }

        .error-message {
            color: #ff4d4d;
            margin-bottom: 16px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div style="">
    <h2>Enter 2FA Code</h2>
    <form method="POST">
        <label for="verification_code">Verification Code:</label>
        <input type="text" id="verification_code" name="verification_code" maxlength="6" required>
        <button type="submit">Verify</button>
    </form>

    
</body>
</html> -->
