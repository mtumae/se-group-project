<?php
// Removed redundant require 'forms.php'
require_once __DIR__ . '/../ClassAutoLoad.php'; // Correct relative path
require_once __DIR__ . '/../DBConnection.php';

$name = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Simple validation
if (empty($name) || empty($email) || empty($password)) {
    die("Error: All fields are required.");
}

if (!isset($conf)) {
    die("Config file not loaded. Check ClassAutoLoad.php.");
}
$db = new database($conf);
$conn = $db->connect();

try {
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Use DEFAULT

    // This INSERT is correct.
    // It only inserts 3 columns. The 4th column, 'role_id',
    // will be set to DEFAULT 2 (the 'user' role) by the database.
    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    
    // Use your 'insert' method from your Database class
    $db->insert($sql, [$name, $email, $hashed_password]);
   
    // Redirect to login page on success
    header("Location: /IAP-GROUP-PROJECT/index.php?form=login&signup=success");
    exit();

} catch (Exception $e) {
    // Error for duplicate email/username
    if ($e->getCode() == 23000) { 
        die("Error: An account with that email or username already exists. <a href='/IAP-GROUP-PROJECT/index.php?form=login'>Go to login</a>");
    } else {
        echo "Error: " . $e->getMessage();
    }
}
?>