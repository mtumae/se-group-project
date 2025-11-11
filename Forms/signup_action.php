<?php
    // require_once '../Validator.php' ;
    require_once 'forms.php';

    require_once 'C:\Apache24\htdocs\SE-GROUP-PROJECT\ClassAutoLoad.php';

    $name = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

   
    require_once 'C:\Apache24\htdocs\SE-GROUP-PROJECT\ClassAutoLoad.php';
    require_once __DIR__ . '/../DBConnection.php';


    $db = new database($conf);
    $conn = $db->connect();
    
    try{

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $db->query("INSERT INTO users (username, email, password) VALUES (?, ?, ?)",[$name, $email, $hashed_password]);
        
        // $stmt->bind_param("sss", $name, $email, $hashed_password);
        // $stmt->execute();
        // $stmt->close();
       
        header("Location: /SE-GROUP-PROJECT/index.php?form=login");
        exit();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
        