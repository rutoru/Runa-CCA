<?php
require '../vendor/autoload.php';
session_start();

// Slim
$app = new \Slim\Slim([
    'templates.path'  => \Base\Conf::TEMPLATES,     // Template
    'cookies.encrypt' => true,
    'debug'           => \Base\Conf::DEBUG,
    'view'            => new \Slim\Views\Twig(),    // Use Twig
    'log.enabled'     => \Base\Conf::LOG_ONOFF,
    'log.level'       => \Base\Conf::LOG_LEVEL,
]);

// Get Route
\Runa_CCA\Route::registration($app);

// Run
$app->run();
