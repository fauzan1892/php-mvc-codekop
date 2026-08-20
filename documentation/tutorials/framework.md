# Framework dan Mulai Cepat

Codekop PHP MVC adalah framework PHP-native untuk aplikasi web dan API. Routing,
controller, model, view, session, CSRF, CSP, dan PDO tersedia tanpa framework
besar. View tetap menggunakan PHP native, database menggunakan PDO, dan asset
UI Retro-term disimpan lokal tanpa CDN.

## Persyaratan dan instalasi

Persyaratan minimum adalah PHP 8.2+, Composer, dan database opsional. PHP 8.4
direkomendasikan.

```bash
composer install
php -S 127.0.0.1:8080
```

Buka `/` untuk halaman utama atau `/api/health` untuk memeriksa endpoint API.
Built-in server hanya untuk development; server tersebut tidak membaca
`.htaccess` dan bukan representasi konfigurasi production.

Pastikan folder berikut dapat ditulis oleh PHP:

- `storage/`
- `storage/sessions/`
- `storage/queue/` jika memakai file queue driver

Atur database pada `app/Config/Database.php`. Untuk production, ubah `env`
menjadi `production` pada `app/Config/Config.php`; jangan memakai development di
server publik.

## Alur request

```text
Browser atau API client
    -> Apache/Nginx rewrite
    -> index.php
    -> security headers dan CSP nonce
    -> app/Config/Config.php
    -> app/Config/Routes.php
    -> System\App
    -> middleware
    -> controller
    -> model/database atau service
    -> view atau JSON Response
```

Semua request aplikasi masuk melalui `index.php`. Dispatcher membatasi
controller ke `app/Controllers` dan hanya memanggil method public yang valid.

## Setup Apache2 dan Nginx

Document root menunjuk langsung ke root project. Semua request yang bukan file
atau folder fisik diarahkan ke `index.php`. Sesuaikan path project, domain, dan
socket PHP-FPM dengan server Anda.

### Apache2

Aktifkan module rewrite dan headers:

```bash
sudo a2enmod rewrite headers
sudo systemctl restart apache2
```

Buat virtual host, misalnya `/etc/apache2/sites-available/codekop.conf`:

```apache
<VirtualHost *:80>
    ServerName codekop.test
    DocumentRoot /var/www/php-mvc-codekop

    <Directory /var/www/php-mvc-codekop>
        AllowOverride All
        Options -Indexes
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/codekop-error.log
    CustomLog ${APACHE_LOG_DIR}/codekop-access.log combined
</VirtualHost>
```

Aktifkan site lalu reload Apache:

```bash
sudo a2ensite codekop.conf
sudo apache2ctl configtest
sudo systemctl reload apache2
```

File `.htaccess` project memblokir akses langsung ke `app/`, `system/`,
`storage/`, `vendor/`, dan `tests/`. `AllowOverride All` harus aktif agar
aturan tersebut dibaca Apache.

### Nginx + PHP-FPM

Gunakan `nginx.conf.example` sebagai dasar server block. Location exact hanya
meneruskan `/index.php`; URL PHP lain harus menghasilkan `404`.

```nginx
server {
    listen 80;
    server_name codekop.test;
    root /var/www/php-mvc-codekop;
    index index.php;

    charset utf-8;
    client_max_body_size 10m;

    location / {
        try_files $uri $uri/ /index.php?url=$uri&$query_string;
    }

    location ~ ^/(app|system|storage|vendor|tests)/ {
        deny all;
        return 404;
    }

    location = /index.php {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param HTTP_PROXY "";
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
    }

    location ~ \.php$ {
        return 404;
    }

    location ~ /\. {
        deny all;
        return 404;
    }
}
```

Uji dan reload Nginx:

```bash
sudo nginx -t
sudo systemctl reload nginx
```

### Permission runtime

Source dapat dibaca PHP-FPM, tetapi hanya `storage/` yang boleh ditulis user
web/worker:

```bash
sudo chown -R deploy:www-data /var/www/php-mvc-codekop
sudo find /var/www/php-mvc-codekop -type d -exec chmod 750 {} \;
sudo find /var/www/php-mvc-codekop -type f -exec chmod 640 {} \;
sudo chown -R www-data:www-data /var/www/php-mvc-codekop/storage
sudo find /var/www/php-mvc-codekop/storage -type d -exec chmod 750 {} \;
sudo find /var/www/php-mvc-codekop/storage -type f -exec chmod 640 {} \;
```

Sesuaikan user/group dengan distro dan konfigurasi PHP-FPM. Jangan memberi
permission write ke seluruh project.

## Struktur project

- `index.php`: front controller, security headers, dan bootstrap awal.
- `app/Config`: konfigurasi aplikasi, database, debug, dan routes.
- `app/Controllers`: controller aplikasi dan action publik.
- `app/Middleware`: middleware aplikasi.
- `app/Models`: model aplikasi.
- `app/Jobs`: job backend yang mengimplementasikan queue contract.
- `app/Views`: template PHP dan halaman error.
- `system/Core`: kernel MVC, router, request, response, session, dan security.
- `system/Queue`: contract, dispatcher, driver, dan worker manager.
- `system/console.php`: perintah CLI, termasuk `queue:work`.
- `assets`: asset UI lokal dan plugin development tanpa CDN.
- `documentation`: pusat reader HTML dan tutorial Markdown.
- `storage`: session runtime dan data internal yang tidak boleh dipublikasi.
- `tests`: PHPUnit unit test dan smoke test akses HTTP.

## Routing web dan API

`app/Config/Routes.php` hanya memanggil `app/Routes/web.php` dan
`app/Routes/api.php`. Route web dan API dipisah per file modul agar mudah
dirawat seperti pola Laravel.

```php
// app/Routes/web.php
use System\Route;

Route::get('/users/{id}', 'Users@show')->name('users.show');
Route::post('/users', 'Users@store');
require_once ROOTPATH . 'app/Routes/master.php';

// app/Routes/api.php
Route::prefix('api')->group(static function (): void {
    Route::get('/health', 'Api@health')->name('api.health');
    Route::post('/echo', 'Api@echo')->name('api.echo');
    require_once ROOTPATH . 'app/Routes/api_master.php';
});
```

Method `get`, `post`, `put`, `patch`, `delete`, dan `options` tetap digunakan
secara normal. Prefix `api` otomatis membentuk URI `/api/...` dan menandai route
sebagai API. Method `apiGet()` lama tetap tersedia untuk kompatibilitas.

Parameter, group, dan resource:

```php
Route::group(['middleware' => ['csrf']], static function (): void {
    Route::post('/users', 'Users@store');
});

Route::resource('/users', 'Users');
```

Method yang tersedia: `get`, `head`, `post`, `put`, `patch`, `delete`, dan
`options`, termasuk variasi API-nya.

## Controller dan model

Controller dapat berupa class global lama atau class namespace PSR-4:

```php
<?php
namespace App\Controllers;

use System\Controller;

final class Users extends Controller
{
    public function show(string $id): array
    {
        return ['id' => $id];
    }
}
```

Model dapat dipanggil dari controller menggunakan loader bawaan:

```php
$users = $this->model('Users');
$users = $this->model('Admin\\Users');
```

Starter tetap menerima dua pola supaya kode lama dari `posv1` dapat dipindahkan
bertahap. Model legacy berupa class global tetap valid:

```php
// app/Models/Test.php
use System\\Models;

class Test extends Models
{
    public function check(): string
    {
        return 'test model';
    }
}

// app/Controllers/Home.php
$test = $this->model('Test');
$result = $test->check();
```

Untuk kode baru, gunakan namespace PSR-4. Model boleh berada di subfolder:

```php
// app/Models/Reports/Sales.php
namespace App\\Models\\Reports;

use System\\Models;

final class Sales extends Models
{
    public function total(): int
    {
        return (int) $this->db?->query(
            'SELECT COUNT(*) FROM sales'
        )->fetchColumn();
    }
}

// controller
$sales = $this->model('Reports\\Sales');
$total = $sales->total();
```

`system/run.php` mendaftarkan autoloader `App\\` dan Composer juga memetakan
namespace tersebut ke `app/`. Loader controller mempertahankan fallback legacy,
sedangkan model PSR-4 dapat langsung dipanggil dari subfolder. Query model tetap
memakai PDO dan prepared statement untuk input dinamis.

## Database dan DebugBar

Isi koneksi pada `app/Config/Database.php`. `System\\Database` menggunakan pool
PDO per request, sehingga controller dan model menggunakan koneksi yang sama.

Pada development dengan PHP 8.2+, `system/run.php` memasang DebugBar v3 dan
collector PDO sebelum controller berjalan. Tab `Database` menampilkan query,
durasi, parameter, dan jumlah statement. Asset DebugBar v3 dibaca dari
`assets/plugins/php-debugbar/dist/` tanpa CDN dan tanpa jQuery. Pada production
DebugBar mati; pada PHP di bawah 8.2 gunakan native diagnostics.

```php
// app/Config/Database.php
$dbconfig = [
    'driver' => 'mysql',
    'hostname' => '127.0.0.1',
    'port' => 3306,
    'database' => 'codekop',
    'username' => 'codekop_app',
    'password' => getenv('CODEKOP_DB_PASSWORD') ?: '',
    'charset' => 'utf8mb4',
];
```

Jangan menyalakan DebugBar di server publik karena query dan detail request
bersifat diagnostik.

## Request dan response

- `$this->request->route('id')`: mengambil parameter route.
- `$this->request->query('q')`: mengambil query string.
- `$this->request->json()`: membaca body JSON sebagai array.
- `Response::json($data)`: menghasilkan response JSON.
- `Response::redirect('/')`: menghasilkan redirect tervalidasi.
- `Request::routeParameters()`: mengambil seluruh parameter route.

## Membuat API dengan bantuan AI

API dibuat dari tiga bagian: route, controller, dan response JSON. Mulai dari
endpoint kecil, uji dengan `curl`, lalu baru sambungkan model dan database.

### 1. Tambahkan route API

Di `app/Routes/api_master.php` atau file modul yang di-require dari
`app/Routes/api.php`:

```php
Route::get('/products', 'Api@products')->name('api.products');
```

### 2. Buat action controller

Di `app/Controllers/Api.php`:

```php
use System\\Response;

public function products(): Response
{
    return Response::json([
        'success' => true,
        'data' => [
            ['id' => 1, 'name' => 'Demo Product'],
        ],
    ]);
}
```

Untuk request JSON gunakan `$this->request->json()`. Untuk query string gunakan
`$this->request->query('search')`. Query database tetap harus menggunakan model
dan prepared statement, bukan menggabungkan input langsung ke SQL.

### 3. Uji endpoint

```bash
curl -i http://localhost/php-mvc-codekop/api/products
curl -i -X POST http://localhost/php-mvc-codekop/api/echo \
  -H 'Content-Type: application/json' \
  -d '{"name":"Demo"}'
```

Response sukses sebaiknya konsisten, misalnya `success`, `data`, dan metadata
pagination bila diperlukan. Error gunakan status HTTP yang benar dan struktur
`error.code` serta `error.message`. Endpoint yang membutuhkan login dapat
menggunakan middleware JWT:

```php
Route::get('/profile', 'Api@profile')
    ->middleware('jwt');
```

### Prompt AI siap pakai

Gunakan prompt berikut saat meminta Codex, Claude, atau agent lain membuat API:

```text
Baca AGENTS.md, README.md, documentation/tutorials/framework.md,
app/Routes/web.php, app/Routes/api.php, app/Controllers/Api.php,
system/Core/Request.php,
system/Core/Response.php, system/Core/Controller.php, dan model yang relevan.

Buat API JSON untuk resource "products" dengan endpoint:
- GET /api/products: list dengan search, page, dan per_page
- GET /api/products/{id}: detail
- POST /api/products: create
- PUT /api/products/{id}: update
- DELETE /api/products/{id}: delete

Aturan implementasi:
- ikuti pola router dan controller project yang sudah ada;
- gunakan Response::json() dan status HTTP yang tepat;
- baca JSON melalui $this->request->json();
- validasi semua input di server-side;
- gunakan model namespace App\\Models\\Products atau pola model yang sudah ada;
- gunakan prepared statements, whitelist kolom/order, dan pagination aman;
- pasang JWT middleware untuk endpoint yang membutuhkan autentikasi;
- jangan menaruh secret atau password di source code;
- tambahkan test untuk sukses, input invalid, 404, 401/403, dan SQL injection;
- jangan menambahkan CDN atau dependency baru tanpa alasan.

Sebelum mengubah file, tampilkan rencana dan file yang akan diubah.
Setelah selesai, jalankan PHPUnit, lint seluruh PHP, dan smoke test curl.
Laporkan contoh request/response, status HTTP, risiko keamanan, dan hasil test.
```

Jangan langsung menerima hasil AI. Periksa route, authorization, query,
escaping response, validasi, dan test secara manual sebelum endpoint dipakai.

## Konfigurasi

Konfigurasi utama ada di `app/Config/Config.php` dan disinkronkan ke
`System\Config`:

```php
$appConfig = [
    'env' => 'development',
    'timezone' => 'Asia/Jakarta',
    'session' => [
        'name' => 'codekop_session',
        'path' => ROOTPATH . 'storage/sessions',
        'cookie_path' => '/',
    ],
    'queue' => [
        'driver' => 'file',
        'default' => 'default',
    ],
];
```

Session memakai strict mode, cookie `HttpOnly`, dan `SameSite`. Detail driver
queue dibahas di tutorial Backend.

## Tautan project

- Source code: [github.com/fauzan1892/php-mvc-codekop](https://github.com/fauzan1892/php-mvc-codekop)
- Retro-term UI: [retro-term.codekopdev.my.id](https://retro-term.codekopdev.my.id/)
- Aturan agent: [`AGENTS.md`](../../AGENTS.md)
