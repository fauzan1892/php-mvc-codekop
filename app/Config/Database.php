<?php
declare(strict_types=1);
namespace Config;

defined('BASEPATH') || exit('No direct script access allowed');

/*
| Database groups, similar to common MVC frameworks.
| Set persistent=true only when the PHP-FPM/Apache process and DB server
| are configured for persistent PDO connections.
*/
$dbconfig = [
    'default' => [
        'driver'     => 'mysql',
        'hostname'   => '127.0.0.1',
        'port'       => 3306,
        'database'   => '',
        'username'   => 'root',
        'password'   => '',
        'charset'    => 'utf8mb4',
        'persistent' => false,
        'options'    => [],
    ],
];

// Backward-compatible active connection config.
$dbconfig = $dbconfig['default'];
