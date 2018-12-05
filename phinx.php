<?php

    $settings = require __DIR__ . '/app/settings.php';

    return array(
        "paths" => array(
            "migrations" => "migrations"
        ),
        "environments" => array(
            "default_migration_table" => "phinxlog",
            "default_database" => "common",
            "common" => array(
                "adapter" => "mysql",
                "host" => $settings['db']['local']['host'],
                "name" => $settings['db']['local']['dbname'],
                "user" => $settings['db']['local']['username'],
                "pass" => $settings['db']['local']['password'],
                "port" => 3306
            )
        )
    );
