<?php

declare(strict_types=1);

namespace Config;

defined('BASEPATH') || exit('No direct script access allowed');

use System\Route;

$routes['DefaultController'] = 'Home::index';

Route::get('/', 'Home@index')->name('home');
Route::get('/home/test', 'Home@test')->name('home.test');

Route::apiGet('/api/health', 'Api@health')->name('api.health');
Route::apiPost('/api/echo', 'Api@echo')->name('api.echo');
Route::apiGet('/api/route/{id}', 'Api@route')->name('api.route');

$routes['routes'] = [];
$routes['active'] = true;
