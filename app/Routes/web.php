<?php
declare(strict_types=1);

namespace Config;

defined('BASEPATH') || exit('No direct script access allowed');

use System\Route;

Route::get('/', 'Home@index')->name('home');
Route::get('/home/test', 'Home@test')->name('home.test');

// Tambahkan route web per modul di file terpisah.
require_once ROOTPATH . 'app/Routes/master.php';
