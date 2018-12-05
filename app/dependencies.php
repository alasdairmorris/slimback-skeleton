<?php

// CHANGEME - Uncomment the following if you want DB functionality.
// $app->container->singleton('redis', function ($container) {
//     return new Predis\Client(null, $container['settings']['redis_config']);
// });

// CHANGEME - Uncomment the following if you want Redis functionality.
// $app->container->singleton('db', function ($container) {
//     $dbSettings = $container['settings']["db"]["local"];
//     $pdo = new PDO($dbSettings["uri"], $dbSettings["username"], $dbSettings["password"]);
//     return new NotORM($pdo);
// });

$app->container->singleton('log', function ($container) {
    $log = new \Monolog\Logger('app');

    $handler = new \Monolog\Handler\StreamHandler(
        sprintf('%s/logs/%s.log', dirname(__DIR__), date('Y-m-d')),
        \Monolog\Logger::DEBUG,
        true,
        0666
    );

    $handler->setFormatter(new \Monolog\Formatter\LineFormatter(null, null, true));
    $log->pushHandler($handler);

    $log->pushProcessor(function ($record){
        $record['extra']['user'] = sprintf("%s(%s)", \App\Authentication::getUsername(), \App\Authentication::getUserId());
        return $record;
    });

    return $log;
});
