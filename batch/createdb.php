<?php

require __DIR__ . '/_batch_init.php';

echo sprintf("CREATE DATABASE %s;\n", $app->settings['db']['local']['dbname']);
echo sprintf("CREATE USER '%s'@'localhost' IDENTIFIED BY '%s';\n", $app->settings['db']['local']['username'], $app->settings['db']['local']['password']);
echo sprintf("GRANT ALL PRIVILEGES ON %s.* TO '%s'@'localhost';\n", $app->settings['db']['local']['dbname'], $app->settings['db']['local']['username']);
