<?php
session_start();
require_once 'forms/forms.php';
require_once 'components/components.php';
$form = new Forms();
$component = new Components();

$component->header();
$component->form_content();
$component->footer();
?>
<link rel="stylesheet" href="index.css">
<?php
// $component->footer();
// $form->signup();
