<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {
    // Global Settings Object
    $containerBuilder->addDefinitions([
        'settings' => [
            'displayErrorDetails' => true, // Should be set to false in production
            'logger' => [
                'name' => 'smoswip',
                'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                'level' => Logger::DEBUG,
            ],
            'db' => [
                'driver' => 'mysql',
                //'host' => '127.0.0.1',
                //'username' => 'root',
                //'password' => 'root',
                //'database' => 'id21337904_smoswip_db',
                'host' => 'srv1048.hstgr.io',
                'username' => 'u640077936_admin',
                'password' => 'LuLKI>90m|R',
                'database' => 'u640077936_smoswip',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'flags' => [
                    // Turn off persistent connections
                    PDO::ATTR_PERSISTENT => false,
                    // Enable exceptions
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    // Emulate prepared statements
                    PDO::ATTR_EMULATE_PREPARES => true,
                    // Set default fetch mode to array
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ],
            ],
            'upload_directory' => __DIR__ . '/../uploads',
            'view' => [
                'path_templates' => __DIR__ . '/../templates'
            ],
        ],
    ]);
};
