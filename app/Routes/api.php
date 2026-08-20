<?php
declare(strict_types=1);

namespace Config;

defined('BASEPATH') || exit('No direct script access allowed');

use System\Route;

// Semua Route::* di dalam prefix ini otomatis diperlakukan sebagai API.
Route::prefix('api')->group(static function (): void {
    Route::get('/health', 'Api@health')->name('api.health');
    Route::post('/echo', 'Api@echo')->name('api.echo');
    Route::get('/route/{id}', 'Api@route')->name('api.route');

    // Tambahkan endpoint API per modul di file terpisah.
    require_once ROOTPATH . 'app/Routes/api_master.php';
});
