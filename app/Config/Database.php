<?php namespace Config;
defined('BASEPATH') or exit('Tidak ada akses skrip langsung diizinkan !');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
*/

// for database mysql and driver connection by default is PDO()
$dbconfig = [
            'hostname' => 'localhost',
            'username' => 'root',
            'password' => '',
            'database' => 'cms_development',
        ];