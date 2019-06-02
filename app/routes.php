<?php

// Routes

$app->get('/', function () use ($app) {
    $app->render('test.html');
})->name('test');

$app->get('/bootstrap-test', function () use ($app) {
    $app->render('bootstrap_test.html');
})->name('bootstrap-test');

// API group
$app->group('/api', function () use ($app) {

});
