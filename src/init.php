<?php

// This code is shared between web interface and cron tasks

define('COZCRASHES_BASE', __DIR__ . '/..');
date_default_timezone_set('UTC');
setlocale (LC_ALL, 'en_US');

require COZCRASHES_BASE . '/vendor/autoload.php';

// Instantiate the app
$config = require COZCRASHES_BASE . '/config.php';
$app = new \Slim\App([
    'settings' => $config['slim']
]);

$container = $app->getContainer();
$container['config'] = $config;
$container->router->setBasePath($config['app']['baseUrl']);
$container['flash'] = function () {
    return new \Slim\Flash\Messages();
};
// Register Twig View helper
$container['view'] = function ($c) {
    $view = new \Slim\Views\Twig(COZCRASHES_BASE . '/templates/', [
        'cache' => false
    ]);
    
    // Instantiate and add Slim specific extension
    $router = $c->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

    $view->addExtension(new \Knlv\Slim\Views\TwigMessages($c->flash));

    return $view;
};
$container['validator'] = function () {
    return new \Rakit\Validation\Validator;
};
$container['db'] = function ($c) {
    $connection = new \Pixie\Connection('mysql', $c->config['db']);
    return new \Pixie\QueryBuilder\QueryBuilderHandler($connection);
};
$container['webhook'] = function ($c) {
    return new \CoZCrashes\Webhook($c);
};
$container['auth'] = function ($c) {
    return new \CoZCrashes\Auth($c);
};
$container['report_util'] = function ($c) {
    return new \CoZCrashes\ReportUtil($c);
};

require_once COZCRASHES_BASE . '/src/util.php';

// Slim likes to overwrite this with nothing when starting dispatch
// so for web accesses we set it again before any real middleware runs
$app->add(function ($request, $response, $next) {
    $this->router->setBasePath($this->config['app']['baseUrl']);
    return $next($request, $response);
});