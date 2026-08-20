# AGENTS.md — Codekop PHP MVC

## Tujuan

Codekop PHP MVC adalah framework MVC PHP-native hasil recode dari aplikasi
lama. Target runtime PHP 8.4+, dengan dukungan PHP 8.2+. Project mendukung AI
coding agents untuk perubahan yang terukur, aman, dan mudah diuji.

## Struktur

- index.php: front controller dan security headers.
- app/Config: konfigurasi aplikasi, database, dan debug.
- app/Routes: route web/API dan route modul yang dipanggil oleh aggregator.
- app/Controllers: controller aplikasi dan action publik.
- app/Models: model aplikasi.
- app/Middleware: middleware route class-based.
- app/Jobs: queued job aplikasi; gunakan namespace App\Jobs dan contract
  System\Queue\Contracts\ShouldQueue.
- app/Views: template PHP; gunakan e() untuk output dinamis.
- system/Core: kernel MVC, dispatcher, routing, session, input, dan security.
- system/Queue: contract job, dispatcher, worker manager, serta file/database
  queue driver.
- system/console.php: entry point command CLI, termasuk queue:work.
- assets: asset lokal Retro-term dan DebugBar.
- storage: session dan queue runtime; jangan menyimpan source code atau data
  publik.
- tests/Unit: PHPUnit unit test dan test internal framework.
- tests/Security: smoke test HTTP untuk Apache/Nginx.

## Aturan perubahan

1. Baca file target dan pertahankan perubahan user yang sudah ada.
2. Gunakan apply_patch untuk edit manual.
3. Jangan commit password, token, atau kredensial.
4. Jangan menonaktifkan CSRF, escaping, CSP, prepared statements, atau path
   protection tanpa alasan dan pengujian yang jelas.
5. Route baru berada di app/Routes/*.php. `app/Config/Routes.php` hanya
   memanggil `web.php` dan `api.php`. Gunakan `Route::get()`/`post()` biasa;
   gunakan `Route::prefix('api')->group(...)` untuk API. Method `apiGet()` lama
   tetap dipertahankan hanya untuk kompatibilitas.
6. Middleware baru berada di app/Middleware/NameMiddleware.php dan harus
   mengimplementasikan System\Middleware.
7. Session tetap berada di storage/sessions, bukan folder publik.
8. Pertahankan strict types dan hindari dynamic properties deprecated.
9. Jangan menambahkan CDN untuk asset yang sudah tersedia lokal. Reader
   dokumentasi harus memakai JavaScript lokal dan merender Markdown dengan
   escaping HTML.
10. Job baru harus berada di app/Jobs, dapat diserialisasi, dan memiliki
    handle(): void. Dispatch memakai System\Queue\Queue; jangan memasukkan
    closure, credential, atau data sensitif ke payload queue.
11. Queue payload hanya boleh berada di storage/queue. Jangan membuat endpoint
    HTTP untuk membaca, mengubah, atau menjalankan payload queue.
12. Pertahankan aturan front-controller-only: hanya index.php yang boleh
    diteruskan ke PHP handler. PHP di assets, tests, app, system, storage,
    vendor, documentation, atau folder lain harus ditolak.
13. Jika mengubah aturan akses server, perbarui .htaccess, nginx.conf.example,
    apache2.conf.example, README.md, dan tests/Security/security_smoke.sh bila
    relevan. Jangan mengandalkan php -S untuk menguji .htaccess.
14. Test kode baru menggunakan PHPUnit di tests/Unit atau tests/Feature.
    Security deployment tetap diuji melalui tests/Security pada Apache/Nginx.
15. Jika menambah atau mengubah dependency, perbarui composer.lock dan jalankan
    Composer validation.

## Verifikasi

Jalankan dari root project:

    composer validate --no-check-publish
    composer test
    find . -path './vendor' -prune -o -name '*.php' -print0 | xargs -0 -n1 php -l

Untuk perubahan akses HTTP, jalankan melalui Apache atau Nginx aktif:

    BASE_URL=http://codekop.test bash tests/Security/security_smoke.sh

Jika perubahan menyentuh runtime, lakukan smoke test melalui Apache lokal dan
cek response HTTP, error log, CSP console, session, serta route yang berubah.

## Mode aplikasi

Mode diatur di app/Config/Config.php. Development menampilkan error dan
mengaktifkan DebugBar; production menyembunyikan detail error. Jangan memakai
mode development di server publik.

## Panduan AI agent

AI agent harus:

- menjelaskan file yang diubah dan alasan teknisnya;
- memeriksa lint atau test yang relevan;
- tidak menjalankan git reset --hard, penghapusan massal, atau perubahan
  konfigurasi server global tanpa persetujuan eksplisit;
- melaporkan blocker jika membutuhkan kredensial, data eksternal, atau akses
  di luar workspace;
- melaporkan jika verifikasi Apache/Nginx tidak dapat dilakukan karena server
  tidak aktif, konfigurasi listener gagal, atau tool server tidak tersedia;
- memperbarui README.md jika public API, struktur folder, instalasi, atau
  konfigurasi berubah;
- memperbarui documentation/index.html dan CHANGELOG.md jika fitur public,
  security baseline, testing, atau struktur project berubah.

## Definition of done

Source valid, tidak menambah deprecated warning yang diketahui, security
baseline tetap aktif, hanya index.php yang dapat dieksekusi web server,
PHPUnit dan lint relevan lulus, dokumentasi public API serta changelog
diperbarui, dan hasil verifikasi dilaporkan.
