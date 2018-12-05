<?php

// Routes

$app->get('/', function () use ($app) {
    $app->render('test.html');
})->name('test');


// API group
$app->group('/api', function () use ($app) {

});
