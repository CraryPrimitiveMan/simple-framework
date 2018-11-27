<?php

define('SF_PATH', dirname(__DIR__));
require_once SF_PATH.'/vendor/autoload.php';
require_once SF_PATH.'/src/Sf.php';

$application = new sf\web\Application();
$application->run();
