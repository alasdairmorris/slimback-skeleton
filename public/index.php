<?php

// Assume all users are on UK time.
date_default_timezone_set('Europe/London');

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../app/settings.php';
$app = new \Slim\Slim($settings);

$app->hook('slim.before', function () use ($app) {
    $packageJSON = json_decode(file_get_contents(__DIR__ . '/../package.json'), true);
    $app->view()->appendData(
        array(
            'baseUrl' => $app->settings['base_url'],
            'appVersion' => $packageJSON['version']
            // 'userObject' => \App\Authentication::getCurrentUserObject($app)
        )
    );
});

// Set up dependencies
require __DIR__ . '/../app/dependencies.php';

// Register middleware
require __DIR__ . '/../app/middleware.php';

// Register routes
require __DIR__ . '/../app/routes.php';

// Run!
$app->run();
