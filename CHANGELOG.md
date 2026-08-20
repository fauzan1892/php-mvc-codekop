# Changelog

Semua perubahan penting pada project ini dicatat di file ini.

## [Unreleased]

### Added

- Memisahkan route ke `app/Routes/web.php` dan `app/Routes/api.php`; `Routes.php`
  kini hanya menjadi aggregator. API mendukung `Route::prefix('api')->group(...)`
  dengan method HTTP biasa, sementara method `apiGet()` lama tetap kompatibel.

- Logo Codekop baru pada `assets/img/codekop-logo.png`.
- Dokumentasi HTML statis pada `/documentation/`.
- Tutorial CRUD dengan Codex/Claude, urutan file yang perlu dibaca, prompt
  implementasi, checklist review AI, dan blueprint RBAC.
- Panduan deployment Apache2, Nginx + PHP-FPM, rewrite, dan permission runtime.
- SEO on-page untuk dokumentasi: metadata, Open Graph, Twitter Card, dan JSON-LD.
- Dukungan route parameter bernama, seperti `/api/route/{id}`.
- Dukungan nested controller dan model dengan namespace `App\\Controllers` dan
  `App\\Models`.
- `Route::group()` untuk middleware bersama.
- `Route::resource()` untuk route CRUD konvensional.
- Route `HEAD`, `OPTIONS`, dan variasi API-nya.
- `Request::route()` dan `Request::routeParameters()`.
- `Response::redirect()` dengan validasi status dan lokasi.
- `CsrfMiddleware` opsional untuk route yang mengubah data.
- Dukungan token CSRF melalui header `X-CSRF-TOKEN`.
- Route contoh `GET /api/route/{id}`.
- Queue backend dengan job di `app/Jobs`, `ShouldQueue`, retry/backoff, worker
  CLI, file driver, dan database/SQLite driver.
- Console command `queue:work` melalui `system/console.php` dan script Composer.
- PHPUnit 10.5, konfigurasi `phpunit.xml`, serta unit test queue di `tests/Unit`.
- Security smoke test HTTP di `tests/Security/security_smoke.sh`.
- Seluruh materi dokumentasi dipindah ke tiga Markdown di
  `documentation/tutorials/`: framework, backend (queue/CRUD/RBAC), dan
  production (security/testing), dengan sidebar dan satu entry point
  `documentation/index.html` sebagai reader.

### Security

- Menambahkan `Cross-Origin-Opener-Policy`.
- Menambahkan `Cross-Origin-Resource-Policy`.
- Menambahkan `X-Permitted-Cross-Domain-Policies`.
- Memperketat CSP dengan `connect-src 'self'`.
- Memblokir akses HTTP ke source, dependency, runtime storage, tests, dan file
  konfigurasi internal melalui Apache `.htaccess` dan konfigurasi Nginx.
- Membatasi eksekusi PHP hanya pada front controller `index.php`.

### Changed

- Merapikan halaman welcome, route test, error 404, dan pusat dokumentasi dengan
  layout minimalis responsif tanpa shadow serta menambahkan kredit Support by
  AITI Solutions, Codekop, dan link repository GitHub.
- Menggunakan logo baru pada welcome view, test view, halaman 404, dan
  dokumentasi.
- Menambahkan PSR-4 autoload untuk namespace `App\\`.
- Merapikan file framework yang disentuh agar mengikuti PSR-12.
- Memperbarui dokumentasi routing dan security di `README.md`.
- Menambahkan dokumentasi queue, PHPUnit, hardening Apache/Nginx, dan testing ke
  `README.md` serta `documentation/index.html`.
- Memendekkan `documentation/index.html` dengan memindahkan tutorial panjang ke
  halaman khusus dan menambahkan checklist deployment production yang aman.
- Menjadikan `documentation/index.html` sebagai reader tunggal yang memuat dan
  merender tutorial Markdown melalui JavaScript lokal tanpa CDN.
- Menambahkan contoh VirtualHost Apache pada `apache2.conf.example`.
- Menyamakan bootstrap starter dengan `posv1`: autoload `App\\` di `system/run.php`,
  dukungan model legacy dan PSR-4, DebugBar v3 dengan asset dist lokal, serta
  collector PDO untuk tab query database.
- Menambahkan tutorial pembuatan API dengan route, controller, response JSON,
  curl, JWT, dan prompt AI siap pakai.
- Menambahkan kredit dokumentasi “Support by” AITI Solutions dan Codekop.

### Verification

- `composer validate --no-check-publish` berhasil, dengan warning license belum
  ditentukan.
- Seluruh file PHP berhasil melewati `php -l`.
- Smoke test `/api/health`, route parameter, route group, dan resource route
  berhasil.
- PHPUnit berhasil: 3 test dan 11 assertion.
- Lint seluruh file PHP dan `git diff --check` berhasil.
