<?php

use Tester\Environment;
use Tester\Helpers;

if (!file_exists(__DIR__ . '/../vendor/autoload.php'))
{
    echo 'Install Nette Tester using `composer update --dev`';
    die(0);
}

require_once __DIR__ . '/../vendor/autoload.php';

Environment::setup();