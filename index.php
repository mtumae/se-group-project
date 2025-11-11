<?php
session_start();
require_once 'forms/forms.php';
require_once 'components/components.php';
$form = new Forms();
$component = new Components();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StrathMart - Home</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
<?php

$component->header();
$component->form_content();
$component->footer();
?>
<?php
// $component->footer();
// $form->signup();
