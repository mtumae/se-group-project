<?php

// require 'Plugins/PHPMailer/vendor/autoload.php';
require_once 'config.php';

$directories = ["Forms", "plugins/Services" ,"Database","templates", "Components"];

spl_autoload_register(function ($className) use ($directories) {
    foreach ($directories as $directory) {
        $filePath = __DIR__ . "/$directory/" . $className . '.php';
        if (file_exists($filePath)) {
            require_once $filePath;
            return;
        }
    }
});

// Create Objects
// $ObjForms   = new Forms();
// $ObjLayout = new layouts();
// $ObjSendMail = new Mail();

// $database = new Database($conf);