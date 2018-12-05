<?php

$settings = array(
    // Slim-specific stuff
    'mode' => 'production',
    'debug' => false,
    'templates.path' => __DIR__ . '/templates',

    // App-specific stuff
    'app_name' => '{{ projectname }}',
    'base_url' => '',
    'public_dir' => __DIR__ . '/../public',
    'db' => array(
        "local" => array("host" => "localhost", "dbname" => "{{ projectname }}", "uri" => "mysql:dbname={{ projectname }};host=localhost", "username" => "CHANGEME", "password" => "CHANGEME"),
    ),
    'redis_config' => array('prefix' => '{{ projectname }}:')

);

// Any local settings can override the above settings.
if(file_exists(__DIR__."/"."local_settings.php"))
{
    $localSettings = require __DIR__."/"."local_settings.php";
    $settings = array_replace_recursive($settings, $localSettings);
}

return $settings;
