<?php

// This code is shared between web interface and cron tasks

require __DIR__ . '/../vendor/autoload.php';

// Instantiate the app
$config = require __DIR__ . '/../config.php';
$app = new \Slim\App([
    'settings' => $config['slim']
]);

$container = $app->getContainer();
$container['config'] = $config;
// Register Twig View helper
$container['view'] = function ($c) {
    $view = new \Slim\Views\Twig(__DIR__ . '/../templates/', [
        'cache' => false
    ]);
    
    // Instantiate and add Slim specific extension
    $router = $c->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

    return $view;
};
$container['validator'] = function () {
    return new \Rakit\Validation\Validator;
};
$container['db'] = function ($c) {
    $connection = new \Pixie\Connection('mysql', $c->config['db']);
    return new \Pixie\QueryBuilder\QueryBuilderHandler($connection);
};