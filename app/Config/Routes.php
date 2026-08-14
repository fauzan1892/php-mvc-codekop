<?php namespace Config;
defined('BASEPATH') OR exit('No direct script access allowed');
use System\Route;

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
*/

// default Controller used
$routes['DefaultController'] = 'Home::index';

// Laravel-style routes. The legacy array format remains supported.
Route::get('/', 'Home@index')->name('home');
Route::get('/home', 'Home@index');
Route::get('/home/test', 'Home@test')->name('home.test');
Route::apiGet('/api/health', 'Api@health')->name('api.health');
Route::apiPost('/api/echo', 'Api@echo')->name('api.echo');
// Route::get('/dashboard', 'Dashboard@index')->middleware('auth');
// Route::get('/admin', 'Admin@index')->middleware(['auth', 'admin']);
// Route::post('/login', 'Auth@login');
// Route::apiGet('/api/profile', 'Api@profile')->middleware('jwt');

$routes['routes'] = [];

/**
 * 
 * 
 * Route mode:
 * TRUE  : try manual routes first, then fall back to direct controller routing.
 * FALSE : use manual routes only; unregistered URLs return a 404 response.
 * 
 */
$routes['active'] = TRUE;
