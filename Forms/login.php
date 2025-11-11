<?php
require_once 'forms.php';
?>
<link rel="stylesheet" href="login.css">
<?php
require_once __DIR__ . '/../ClassAutoLoad.php';
$components = new Components();
$forms = new Forms();

// $components->header();
$forms->login();