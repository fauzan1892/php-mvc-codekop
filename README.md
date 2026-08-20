# Codekop PHP MVC

Codekop PHP MVC adalah mini-framework PHP-native hasil recode dari aplikasi
PHP MVC Codekop lama. Project ini dibuat untuk menyediakan fondasi aplikasi web
dan API yang ringan, mudah dipahami, aman secara default, serta nyaman
dikembangkan manusia maupun AI coding agent.

Framework ini mengambil ide dari CodeIgniter 3 dan Laravel, tetapi tidak
bergantung pada framework besar. View tetap menggunakan PHP native, routing
mendukung gaya Laravel, database menggunakan PDO, dan asset UI Retro-term
disimpan lokal tanpa CDN.

Support by [AITI Solutions](https://aiti-solutions.com/) dan
[Codekop](https://www.codekop.com/).

## Tujuan project

Tujuan utama recode ini:

- memodernisasi source lama untuk PHP 8.2+ dan PHP 8.4;
- menghilangkan dynamic property dan pola deprecated PHP modern;
- menyediakan struktur MVC yang jelas;
- menyediakan front controller tunggal;
- menyediakan routing manual dan fallback direct controller;
- menyediakan API JSON dalam kernel yang sama;
- menyediakan middleware session dan JWT;
- menyediakan session manager dengan storage terpisah;
- menerapkan baseline keamanan OWASP;
- menyediakan templating PHP native bergaya CodeIgniter 3;
- menyediakan DebugBar untuk development;
- menyediakan UI Retro-term lokal;
- membuat repository mudah dipahami dan dioperasikan oleh AI agent.

Project ini adalah recode. Kode lama tidak dianggap sebagai kontrak internal
yang harus dipertahankan seluruhnya. Kompatibilitas dipertahankan pada bagian
yang berguna, seperti controller dasar, helper, session API lama, routing array,
dan pemanggilan view CodeIgniter-style.

## Status kemampuan

Yang sudah tersedia:

- MVC web;
- routing HTTP method;
- route name;
- middleware class-based;
- guard callback legacy;
- API JSON;
- request parser untuk form dan JSON;
- response status, header, text, dan JSON;
- JWT HS256 middleware;
- session file storage;
- CSRF token;
- PDO MySQL/MariaDB dan SQLite;
- prepared statements;
- CSP nonce;
- DebugBar development;
- Retro-term dark/light mode;
- Apache rewrite;
- contoh konfigurasi Nginx;
- dokumentasi untuk AI agent.

Fitur boilerplate tambahan:

- nested controller dan model dengan namespace `App\\Controllers` serta
  `App\\Models`;
- route parameter bernama, misalnya `/api/route/{id}`;
- route group dengan middleware;
- `Route::resource()` untuk route CRUD konvensional;
- `Request::route()` dan `Request::routeParameters()`;
- `Response::redirect()`;
- CSRF token dari form maupun header `X-CSRF-TOKEN`;
- `CsrfMiddleware` opsional untuk route `POST`, `PUT`, `PATCH`, dan `DELETE`;
- security headers tambahan untuk isolasi origin dan CSP `connect-src`.
- queue backend dengan job di `app/Jobs`, worker CLI, retry/backoff, dan driver
  file atau database.

Boilerplate ini tidak memaksakan auth atau login. Middleware bersifat opsional
dan dapat ditambahkan oleh aplikasi pemakai sesuai kebutuhan.

Yang harus ditambahkan oleh aplikasi pemakai sesuai kebutuhan:

- autentikasi user lengkap;
- migrasi database;
- rate limiting;
- policy atau permission yang lebih detail;
- scheduler;
- upload validation;
- audit log bisnis;
- observability production.

## Persyaratan

- PHP 8.2 atau lebih baru; PHP 8.4 direkomendasikan;
- Composer;
- Apache dengan mod_rewrite atau Nginx dengan PHP-FPM;
- MySQL/MariaDB atau SQLite;
- folder project dapat dibaca web server;
- folder storage/sessions dapat ditulis PHP.

## Struktur project

    index.php
        Front controller, security headers, dan bootstrap awal.

    composer.json
    composer.lock
        Dependency dan versi dependency yang dikunci.

    AGENTS.md
        Instruksi khusus untuk AI agent dan maintainer repository.

    app/
      Config/
        Config.php
            Sumber konfigurasi aplikasi berbentuk array.
        Constants.php
            Kompatibilitas constant base_url dan constant lama.
        Database.php
            Konfigurasi koneksi database.
        Debug.php
            Error handler development/production.
        Routes.php
            Aggregator yang hanya memanggil route web.php dan api.php.
      Routes/
        web.php
            Route halaman web dan require route modul web.
        api.php
            Group prefix API dan require route modul API.
        master.php, api_master.php
            Contoh route per modul.
      Controllers/
        Home.php
        Api.php
            Controller aplikasi.
      Middleware/
        AuthMiddleware.php
        AdminMiddleware.php
        JwtMiddleware.php
            Middleware aplikasi.
      Models/
            Model aplikasi.
      Views/
            Template PHP dan halaman error.
      Jobs/
            Job backend yang mengimplementasikan `System\Queue\Contracts\ShouldQueue`.
      Helper/
            Helper aplikasi tambahan.

    system/
      Config/Config.php
          Runtime config accessor.
      Core/
        App.php
            Dispatcher dan controller resolver.
        Route.php
            Router web/API dan middleware resolver.
        Middleware.php
            Kontrak middleware.
        Request.php
            HTTP request, query, form, JSON, header, dan bearer token.
        Response.php
            Response text, JSON, no-content, header, dan status.
        Controller.php
            Base controller.
        Views.php
            View loader dan layout/section.
        Input.php
            API input lama dan CSRF check.
        Session.php
            Session manager.
        Security.php
            CSRF token.
        Crud.php
            CRUD PDO dengan validasi identifier.
        Models.php
            Base model.
      Database.php
          Connection manager PDO.
      Queue/
          Queue contract, dispatcher, driver, dan worker manager.
      console.php
          Perintah CLI, termasuk `queue:work`.
      Helper.php
          Helper global.
      run.php
          Bootstrap framework.

    assets/
      retro-term/
          CSS, JavaScript, dan icon Retro-term.
      img/
          Logo dan gambar lokal.
      plugins/php-debugbar/
          Asset DebugBar lokal.

    documentation/
      index.html
          Pusat dokumentasi dan reader Markdown berbasis JavaScript lokal.
      tutorials/
          Tiga materi Markdown: framework, backend, dan production aman.

    storage/
      sessions/
          File session runtime.
      queue/
        pending/ processing/ failed/
            Payload queue file driver; seluruh folder ditolak dari HTTP.
      .htaccess
          Penolakan akses HTTP langsung.
    tests/
      Unit/
            PHPUnit unit test untuk queue dan komponen framework.
      Security/
            Smoke test akses HTTP Apache/Nginx.

## Alur request

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

Semua request aplikasi masuk melalui index.php. Dispatcher membatasi controller
ke app/Controllers dan hanya memanggil method public yang valid.

## Instalasi

Dari root project:

    composer install

Pastikan folder berikut dapat ditulis oleh PHP:

    storage/
    storage/sessions/

Salin konfigurasi database pada app/Config/Database.php sesuai kebutuhan.

Untuk development:

    app/Config/Config.php
        'env' => 'development'

Untuk production:

    app/Config/Config.php
        'env' => 'production'

Jangan memakai development di server publik.

## Konfigurasi utama

Sumber konfigurasi aplikasi berada di app/Config/Config.php. Konfigurasi
berbentuk array, lalu disinkronkan otomatis ke System\Config.

    $appConfig = [
        'env' => 'development',
        'timezone' => 'Asia/Jakarta',
        'session' => [
            'name' => 'codekop_session',
            'path' => ROOTPATH . 'storage/sessions',
            'cookie_path' => '/',
            'same_site' => 'Lax',
        ],
        'api' => [
            'jwt_secret' => '',
            'jwt_leeway' => 0,
        ],
        'queue' => [
            'driver' => 'file',
            'default' => 'default',
            'path' => ROOTPATH . 'storage/queue',
            'retry_after' => 90,
            'sleep' => 3,
            'max_attempts' => 3,
        ],
    ];

Runtime accessor:

    \System\Config::get('timezone');
    \System\Config::get('session.name');
    \System\Config::get('api.jwt_secret');
    \System\Config::get('queue.driver');
    \System\Config::all();

API lama tetap tersedia:

    \System\Config::baseURL();
    \System\Config::$siteURL;
    \System\Config::$timeZone;

Constant seperti APP_ENV, SESSION_NAME, SESSION_PATH, dan API_JWT_SECRET dibuat
otomatis untuk kompatibilitas kode lama. Kode baru sebaiknya membaca
System\Config atau APP_CONFIG.

Timezone default adalah Asia/Jakarta. PHP runtime juga menggunakan timezone
tersebut untuk date dan timestamp aplikasi.

## Routing web dan API

`app/Config/Routes.php` hanya menjadi aggregator. Route web ditulis di
`app/Routes/web.php`, sedangkan route API ditulis di `app/Routes/api.php` atau
file modul yang di-require dari sana.

    // app/Routes/web.php
    use System\Route;

    Route::get('/', 'Home@index')->name('home');
    Route::get('/home/test', 'Home@test')->name('home.test');
    require_once ROOTPATH . 'app/Routes/master.php';

    // app/Routes/api.php
    Route::prefix('api')->group(static function (): void {
        Route::get('/health', 'Api@health')->name('api.health');
        Route::post('/echo', 'Api@echo')->name('api.echo');
        require_once ROOTPATH . 'app/Routes/api_master.php';
    });

Semua method HTTP biasa (`get`, `post`, `put`, `patch`, `delete`, `options`)
di dalam prefix `api` otomatis memiliki URI `/api/...` dan diperlakukan sebagai
response API. `apiGet()` dan method API lama tetap tersedia untuk kompatibilitas.

Parameter route tersedia melalui request:

    Route::get('/users/{id}', 'Users@show');
    $id = $this->request->route('id');

Group middleware dan resource route:

    Route::group(['middleware' => ['csrf']], static function (): void {
        Route::post('/users', 'Users@store');
    });
    Route::resource('/users', 'Users');

Route API contoh yang tersedia pada boilerplate:

    GET  /api/health
    POST /api/echo
    GET  /api/route/{id}

Action berikut sama-sama didukung:

    Home@index
    Home::index

HTTP method yang tersedia:

    Route::get()
    Route::head()
    Route::post()
    Route::put()
    Route::patch()
    Route::delete()
    Route::options()

Parameter URI sederhana:

    Route::get('/users/{id}', 'User@show');

Parameter diteruskan sesuai urutan ke action controller dan juga tersedia
melalui `Request::route()`.

## Mode routes active

Di app/Config/Routes.php:

    $routes['active'] = true;

Jika active TRUE:

1. route manual dicoba lebih dulu;
2. jika tidak ada, framework memakai fallback direct controller.

Contoh fallback:

    /home/test
        -> Home::test

Jika active FALSE:

- hanya route manual yang boleh berjalan;
- URL yang tidak terdaftar menjadi 404;
- direct controller tidak digunakan.

Mode FALSE direkomendasikan untuk production jika semua endpoint sudah
didefinisikan eksplisit.

Format route legacy tetap didukung:

    $routes['routes'] = [
        'GET /profile' => 'Profile::index',
    ];

## MVC dan templating

Controller mewarisi System\Controller:

    use System\Controller;

    final class Home extends Controller
    {
        public function index(): void
        {
            $this->load->view('welcome_view', [
                'title' => 'Dashboard',
            ]);
        }
    }

Property controller yang tersedia:

    $this->db
    $this->session
    $this->input
    $this->request
    $this->response
    $this->security
    $this->crud
    $this->load
    $this->show

View berada di app/Views:

    $this->load->view('welcome_view', $data);
    $this->show->view('welcome_view', $data);

Helper view:

    <?= e($value) ?>
    <?= csrf_field() ?>
    <?= base_url('assets/app.css') ?>

Gunakan e() untuk semua output yang berasal dari user, database, request, atau
sumber eksternal.

Layout dan section tersedia melalui Views:

    $this->load->layout('layouts/app', $data);
    $this->load->section('content');
    $this->load->endSection();
    $this->load->yield('content');

Retro-term digunakan sebagai template default halaman welcome dan error 404.
Asset UI disimpan lokal agar development tidak bergantung pada CDN.

## API

API memakai router dan controller yang sama:

    // app/Routes/api_master.php, dipanggil dari app/Routes/api.php
    Route::get('/health', 'Api@health');
    Route::post('/echo', 'Api@echo');

Endpoint bawaan:

    GET  /api/health
    POST /api/echo

Controller API dapat mengembalikan Response:

    use System\Response;

    public function health(): Response
    {
        return Response::json([
            'success' => true,
            'data' => ['status' => 'ok'],
        ]);
    }

Controller API juga dapat mengembalikan array biasa:

    public function profile(): array
    {
        return ['id' => 1, 'name' => 'Demo'];
    }

Framework mengubah return array menjadi:

    {
        "data": {
            "id": 1,
            "name": "Demo"
        }
    }

Request API:

    $payload = $this->request->json();
    $name = $this->request->input('name');
    $query = $this->request->query('search');
    $authorization = $this->request->header('Authorization');
    $token = $this->request->bearerToken();
    $jwt = $this->request->jwtPayload();

Response API:

    return Response::json($data, 201);
    return Response::text('Accepted', 202);
    return Response::noContent();

### Prompt AI untuk membuat API

Minta AI membaca pola project lebih dulu, lalu gunakan prompt terukur seperti
berikut:

    Baca AGENTS.md, README.md, app/Routes/web.php, app/Routes/api.php,
    app/Controllers/Api.php, system/Core/Request.php,
    system/Core/Response.php, dan model yang relevan.

    Buat API JSON resource products:
    GET /api/products dengan search, page, per_page;
    GET /api/products/{id};
    POST /api/products;
    PUT /api/products/{id};
    DELETE /api/products/{id}.

    Ikuti pola router/controller project. Gunakan Response::json(), validasi
    server-side, prepared statements, whitelist order/kolom, status HTTP yang
    tepat, JWT middleware untuk endpoint privat, dan test sukses/invalid/404/
    401/403/SQL injection. Jangan menambah CDN atau dependency baru.

    Sebelum edit tampilkan rencana dan file yang diubah. Setelah edit jalankan
    PHPUnit, lint PHP, dan smoke test curl. Laporkan contoh request/response,
    risiko keamanan, dan hasil verifikasi.

Error API menggunakan JSON:

    {
        "error": {
            "code": "not_found",
            "message": "Resource not found"
        }
    }

## Middleware

Middleware aplikasi berada di app/Middleware. Kontraknya:

    namespace App\Middleware;

    final class AuthMiddleware implements \System\Middleware
    {
        public function handle(): bool
        {
            return isset($_SESSION['user_id']);
        }
    }

Pakai di route:

    Route::get('/dashboard', 'Dashboard@index')
        ->middleware('auth');

Beberapa middleware dapat dipasang:

    Route::get('/admin', 'Admin@index')
        ->middleware(['auth', 'admin']);

Nama auth akan mencari:

    app/Middleware/AuthMiddleware.php

Jika middleware mengembalikan false, controller tidak dibuat dan request
mendapat HTTP 403. Route::guard('name', callable) tetap didukung untuk
kompatibilitas aplikasi lama.

## Session

Session memakai file storage khusus:

    storage/sessions/

Cookie session memiliki:

- HttpOnly;
- SameSite Lax;
- strict session mode;
- cookie-only;
- Secure otomatis ketika HTTPS.

API modern:

    $this->session->set('user_id', $user->id);
    $userId = $this->session->get('user_id');
    $exists = $this->session->has('user_id');
    $this->session->forget('user_id');
    $this->session->flash('status', 'Profil tersimpan');
    $message = $this->session->getFlash('status');
    $this->session->invalidate();

API kompatibilitas lama:

    $this->session->set_userdata('user_id', 1);
    $this->session->userdata('user_id');
    Session::set_flashdata('Pesan', 'Aksi', 'success');
    Session::flashdata();

Setelah login, regenerasi session ID:

    $this->session->regenerate();

Saat logout:

    $this->session->invalidate();

## JWT API

JWT tersedia sebagai middleware opsional. Konfigurasi pada array api:

    'api' => [
        'jwt_secret' => 'secret-random-minimal-32-karakter',
        'jwt_leeway' => 0,
    ],

Pasang:

    Route::get('/profile', 'Api@profile')
        ->middleware('jwt');

Kirim header:

    Authorization: Bearer <token>

Implementasi middleware saat ini:

- algoritma HS256;
- signature diverifikasi dengan hash_equals;
- secret kosong ditolak;
- klaim exp diverifikasi jika ada;
- klaim nbf diverifikasi jika ada;
- payload valid tersedia melalui $this->request->jwtPayload().

JWT secret wajib disimpan sebagai secret deployment pada production. Jangan
commit secret production ke repository.

## Database PDO

Konfigurasi berada di app/Config/Database.php:

    $dbconfig = [
        'driver' => 'mysql',
        'hostname' => '127.0.0.1',
        'port' => 3306,
        'database' => 'nama_database',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
        'persistent' => false,
        'options' => [],
    ];

System\Database menyediakan:

- MySQL/MariaDB;
- SQLite;
- PDO exception mode;
- associative fetch;
- native prepared statements;
- emulated prepares disabled;
- utf8mb4;
- cached connection selama request;
- opsi persistent PDO.

Gunakan:

    $database = new \System\Database();
    $pdo = $database->connect();

Jangan menggabungkan input user ke SQL. Gunakan prepared statement. Batasi
identifier tabel dan kolom melalui whitelist atau validasi identifier.

## Queue dan job backend

Queue memakai job class di `app/Jobs` dan worker CLI di `system/console.php`.
Job harus menggunakan namespace `App\Jobs` dan mengimplementasikan contract
`System\Queue\Contracts\ShouldQueue`. Base class `System\Queue\Job` memberi
property `queue`, `tries`, `backoff`, dan callback `failed()`.

Contoh job:

    <?php
    namespace App\Jobs;

    use System\Queue\Job;

    final class SendReportJob extends Job
    {
        public int $tries = 5;
        public int|array $backoff = [10, 30, 60];

        public function __construct(public readonly int $reportId) {}

        public function handle(): void
        {
            // proses report berdasarkan $this->reportId
        }
    }

Dispatch dari controller atau service:

    use App\Jobs\SendReportJob;
    use System\Queue\Queue;

    $id = Queue::dispatch(new SendReportJob($reportId));
    Queue::dispatch(new SendReportJob($reportId), 'reports', 30);

Jalankan worker dari root project:

    php system/console.php queue:work
    php system/console.php queue:work --queue=reports --sleep=5
    php system/console.php queue:work --once

Driver default adalah `file`, sehingga tidak membutuhkan service tambahan.
Payload berada di `storage/queue` dan folder tersebut ditolak dari HTTP. Untuk
production multi-worker, driver database dapat dipilih:

    'queue' => [
        'driver' => 'database',
        'database' => [
            'table' => 'jobs',
            'failed_table' => 'failed_jobs',
            'auto_create' => true,
        ],
    ],

Driver database memakai koneksi PDO aplikasi dan membuat tabel queue saat
pertama kali dipakai jika `auto_create` aktif. Job yang melewati jumlah `tries`
atau `max_attempts` dipindahkan ke failed storage. Worker memulihkan reservation
yang stale berdasarkan `retry_after`.

## Security baseline

Baseline yang tersedia:

- CSP dengan nonce;
- style-src dan script-src self;
- style-src-attr unsafe-inline hanya pada development untuk tooling DebugBar;
- X-Content-Type-Options nosniff;
- X-Frame-Options SAMEORIGIN;
- Referrer-Policy;
- Permissions-Policy;
- HSTS saat HTTPS;
- secure HttpOnly session cookie;
- CSRF token random_bytes dan hash_equals;
- escaping HTML melalui e();
- prepared statements;
- validasi controller dan method;
- validasi identifier CRUD;
- blok akses HTTP ke app, system, storage, vendor, dan tests;
- hanya `index.php` yang boleh diteruskan ke PHP handler;
- file PHP di assets atau folder lain ditolak;
- file Composer, environment, dokumentasi internal (README, CHANGELOG, AGENTS),
  dan dot-directory ditolak; dokumentasi HTML publik tetap tersedia.
- directory listing dimatikan;
- flash message di-escape;
- error production tidak membocorkan detail internal.

CSRF wajib diverifikasi pada form atau endpoint session yang mengubah data:

    if (!$this->input->csrf()) {
        http_response_code(419);
        exit('Invalid CSRF token');
    }

API dengan JWT menggunakan signature validation, tetapi aplikasi tetap harus
menerapkan authorization berdasarkan role, ownership, scope, atau policy.
Authentication bukan authorization.

## Mode development dan production

Development:

- APP_DEBUG aktif;
- detail error ditampilkan;
- DebugBar aktif jika dependency tersedia;
- style attribute tooling diizinkan oleh CSP.

Production:

- DebugBar mati;
- detail exception tidak dikirim ke client;
- error dicatat ke log server;
- CSP tidak mengizinkan style attribute inline;
- gunakan HTTPS;
- gunakan database user non-root.

Periksa app/Config/Config.php sebelum deploy.

## Controller, model, dan DebugBar

Starter ini kompatibel dengan pola model `posv1`:

```php
// model legacy: app/Models/Test.php
$test = $this->model('Test');

// model PSR-4 di app/Models/Reports/Sales.php
$sales = $this->model('Reports\\Sales');
```

`system/run.php` mendaftarkan autoloader `App\\` untuk controller, model, job,
dan subfolder PSR-4. Model legacy global tetap didukung oleh loader pada base
controller, jadi migrasi dari `posv1` dapat dilakukan bertahap.

Konfigurasi database berada di `app/Config/Database.php`. Pada development
dengan PHP 8.2+, DebugBar v3 aktif sebelum controller berjalan dan memasang
collector PDO. Setelah ada query, tab `Database` menampilkan SQL, parameter,
durasi, dan jumlah statement. Asset dibaca dari `assets/plugins/php-debugbar/dist/`
secara lokal tanpa CDN dan tanpa jQuery. Pada production DebugBar selalu mati.

## DebugBar

Dependency DebugBar dipasang melalui Composer:

    composer install

DebugBar v3 hanya aktif saat env development dan PHP 8.2+. Asset dist disajikan
lokal:

    assets/plugins/php-debugbar/dist/

Jika DebugBar tidak muncul:

1. pastikan env development;
2. pastikan vendor/autoload.php ada;
3. pastikan `assets/plugins/php-debugbar/dist/debugbar.min.css` dan
   `debugbar.min.js` ada;
4. cek CSP browser;
5. cek response memiliki penutup body;
6. hapus cache browser lalu reload.

DebugBar dimatikan otomatis pada production.

## Apache lokal

Project mendukung `.htaccess` pada root, app, system, storage, assets, dan tests.
Root `.htaccess` mengirim request yang bukan file/folder ke `index.php`, tetapi
menolak seluruh PHP selain front controller.

Pastikan:

- mod_rewrite aktif;
- AllowOverride All aktif;
- `apache2.conf.example` dipakai sebagai referensi VirtualHost;
- PHP 8.4 digunakan Apache;
- php84.local diarahkan ke 127.0.0.1 jika memakai host tersebut.

Contoh setup Debian/Ubuntu:

    sudo a2enmod rewrite headers
    sudo a2ensite codekop.conf
    sudo apachectl configtest
    sudo systemctl reload apache2

Jangan mengatur `AllowOverride None` untuk folder project, karena aturan
penolakan `.htaccess` tidak akan dibaca.

Contoh URL:

    http://php84.local:8080/php-mvc-codekop/
    http://php84.local/php-mvc-codekop/

Sesuaikan port dengan konfigurasi Apache lokal.

## Nginx

Gunakan nginx.conf.example sebagai template. Sesuaikan:

- server_name;
- root;
- socket PHP-FPM;
- permission storage;
- deny rule app, system, storage, vendor, dan tests;
- lokasi PHP yang hanya mengizinkan `/index.php`.

Validasi:

    nginx -t

Reload:

    sudo systemctl reload nginx

## Security smoke test

Security test harus dijalankan melalui Apache atau Nginx aktif; `php -S` tidak
memproses `.htaccess` sehingga tidak dapat memvalidasi konfigurasi Apache.

    BASE_URL=http://codekop.test bash tests/Security/security_smoke.sh

Test ini memeriksa bahwa `app`, `system`, `storage`, `vendor`, `tests`, file
Composer, `.env`, dan PHP selain `index.php` menghasilkan status `403` atau
`404`. `index.php` tetap boleh diproses oleh PHP-FPM/Apache.

## PHPUnit

Test kode menggunakan PHPUnit 10.5 agar tetap kompatibel dengan PHP 8.1+ pada
tooling test. Test unit berada di `tests/Unit`, sedangkan test akses HTTP tetap
berada di `tests/Security` karena membutuhkan Apache atau Nginx aktif.

Jalankan seluruh test:

    composer test
    vendor/bin/phpunit

## Verifikasi project

Lint seluruh source:

    find . -path './vendor' -prune -o -name '*.php' -print0 | xargs -0 -n1 php -l

Validasi Composer:

    composer validate --no-check-publish

Smoke test web:

    REQUEST_URI=/ SCRIPT_NAME=/index.php SERVER_NAME=localhost php index.php

Smoke test API:

    REQUEST_URI=/api/health SCRIPT_NAME=/index.php SERVER_NAME=localhost php index.php

Yang perlu diverifikasi setelah perubahan:

- status HTTP;
- response HTML atau JSON;
- header CSP;
- session cookie;
- error log;
- route manual;
- fallback direct controller;
- middleware;
- query database;
- tampilan Retro-term.

## Dukungan AI agent

Repository ini secara resmi disiapkan untuk AI coding agent. Tujuannya agar
agent dapat memahami konteks project, batas keamanan, struktur file, dan cara
verifikasi tanpa menebak-nebak kontrak aplikasi.

Instruksi operasional agent berada di AGENTS.md. File tersebut menjelaskan:

- tujuan project;
- struktur folder;
- aturan edit;
- aturan keamanan;
- perintah lint dan verifikasi;
- batas destructive action;
- definition of done.

AI agent yang bekerja di repository ini harus:

1. membaca README.md dan AGENTS.md sebelum perubahan besar;
2. memeriksa file target sebelum mengedit;
3. mempertahankan perubahan user yang sudah ada;
4. memakai apply_patch untuk edit manual;
5. tidak menyimpan secret, token, atau password;
6. tidak menonaktifkan CSP, CSRF, escaping, atau prepared statement tanpa
   alasan yang jelas;
7. memperbarui dokumentasi jika public API atau struktur berubah;
8. menjalankan lint dan smoke test yang relevan;
9. melaporkan file yang diubah, hasil test, dan risiko yang tersisa;
10. tidak menjalankan git reset --hard atau penghapusan massal.

AI agent boleh membantu:

- membuat controller, model, view, route, middleware, dan API;
- melakukan audit security;
- membaca dan menjelaskan struktur MVC;
- memperbaiki deprecated PHP;
- membuat test dan smoke test;
- memperbarui dokumentasi;
- menyiapkan konfigurasi Apache atau Nginx;
- membuat endpoint JSON;
- mengintegrasikan session atau JWT sesuai konfigurasi.

AI agent tetap membutuhkan keputusan maintainer untuk:

- credential production;
- perubahan database production;
- migrasi destruktif;
- deployment;
- perubahan server global;
- penghapusan data;
- perubahan security policy yang memperlemah proteksi.

## Definition of done

Perubahan dianggap selesai jika:

- source PHP valid;
- tidak menambah deprecated warning yang diketahui;
- security baseline tetap aktif;
- route web dan API sesuai tujuan;
- response memiliki format yang benar;
- dokumentasi public API diperbarui;
- lint atau test relevan berhasil;
- risiko atau keterbatasan dilaporkan.

## Production checklist

- ubah env menjadi production;
- gunakan HTTPS;
- isi JWT secret dari secret manager atau environment deployment;
- gunakan database user non-root;
- jangan commit password;
- pastikan storage tidak dapat diakses HTTP;
- pastikan vendor tidak menyajikan source PHP;
- matikan directory listing;
- batasi upload dan request body;
- pasang rate limit login dan endpoint sensitif;
- gunakan authorization berbasis role atau policy;
- aktifkan audit log;
- lakukan backup database;
- update dependency Composer secara berkala;
- review CSP dan response headers;
- jalankan lint dan smoke test sebelum release.

## Lisensi dan kontribusi

Project ini ditujukan sebagai fondasi pengembangan aplikasi PHP MVC dan API.
Kontribusi sebaiknya fokus pada keamanan, kompatibilitas PHP modern,
kesederhanaan API, dokumentasi, dan kemampuan observability.
