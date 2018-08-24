<?php

session_start();

require_once __DIR__ . '/../src/init.php';
require_once COZCRASHES_BASE . '/src/routes.php';

// Run app
$app->run();
