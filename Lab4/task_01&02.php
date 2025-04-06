<?php
require_once 'FormBuilder.php';
require_once 'SafeFormBuilder.php';

$formBuilder = new SafeFormBuilder(FormBuilder::METHOD_POST, '/destination.php', 'Send!');
$formBuilder
    ->addTextField('username', 'Guest')
    ->addRadioGroup('gender', ['Male', 'Female'])
    ->addCheckbox('subscribe', 'yes', true)
    ->addTextarea('comment', 'Enter your comments here');

echo $formBuilder->getForm();