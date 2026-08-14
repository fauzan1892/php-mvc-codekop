# Codekop PHP MVC

Codekop PHP MVC adalah mini-framework PHP-native hasil recode dari aplikasi
PHP MVC Codekop lama. Project ini dibuat untuk menyediakan fondasi aplikasi web
dan API yang ringan, mudah dipahami, aman secara default, serta nyaman
dikembangkan manusia maupun AI coding agent.

Framework ini mengambil ide dari CodeIgniter 3 dan Laravel, tetapi tidak
bergantung pada framework besar. View tetap menggunakan PHP native, routing
mendukung gaya Laravel, database menggunakan PDO, dan asset UI Retro-term
disimpan lokal tanpa CDN.

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

Yang harus ditambahkan oleh aplikasi pemakai sesuai kebutuhan:

- autentikasi user lengkap;
- migrasi database;
- rate limiting;
- policy atau permission yang lebih detail;
- queue dan scheduler;
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
            Route web, API, dan middleware.
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

    storage/
      sessions/
          File session runtime.
      .htaccess
          Penolakan akses HTTP langsung.

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
    ];

Runtime accessor:

    \System\Config::get('timezone');
    \System\Config::get('session.name');
    \System\Config::get('api.jwt_secret');
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

## Routing web

Route didefinisikan di app/Config/Routes.php:

    <?php namespace Config;

    use System\Route;

    Route::get('/', 'Home@index')->name('home');
    Route::get('/home/test', 'Home@test')->name('home.test');
    Route::post('/login', 'Auth@login');

Action berikut sama-sama didukung:

    Home@index
    Home::index

HTTP method yang tersedia:

    Route::get()
    Route::post()
    Route::put()
    Route::patch()
    Route::delete()

Parameter URI sederhana:

    Route::get('/users/{id}', 'User@show');

Parameter digunakan untuk pencocokan route. Pengambilan parameter controller
dapat dikembangkan sesuai kebutuhan aplikasi.

## Mode routes active

Di app/Config/Routes.php:

    $routes['active'] = TRUE;

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

    Route::apiGet('/api/health', 'Api@health');
    Route::apiPost('/api/echo', 'Api@echo');

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

    Route::apiGet('/api/profile', 'Api@profile')
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
- blok akses HTTP ke app, system, storage, dan vendor;
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

## DebugBar

Dependency DebugBar dipasang melalui Composer:

    composer install

DebugBar hanya aktif saat env development. Asset disajikan lokal:

    assets/plugins/php-debugbar/dist/

Jika DebugBar tidak muncul:

1. pastikan env development;
2. pastikan vendor/autoload.php ada;
3. pastikan debugbar.min.css dan debugbar.min.js ada;
4. cek CSP browser;
5. cek response memiliki penutup body;
6. hapus cache browser lalu reload.

DebugBar dimatikan otomatis pada production.

## Apache lokal

Project mendukung .htaccess pada root, app, system, dan storage. Root .htaccess
mengirim request yang bukan file/folder ke index.php.

Pastikan:

- mod_rewrite aktif;
- AllowOverride All aktif;
- PHP 8.4 digunakan Apache;
- php84.local diarahkan ke 127.0.0.1 jika memakai host tersebut.

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
- deny rule app, system, storage, dan vendor.

Validasi:

    nginx -t

Reload:

    sudo systemctl reload nginx

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
