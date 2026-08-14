# AGENTS.md — Codekop PHP MVC

## Tujuan

Codekop PHP MVC adalah framework MVC PHP-native hasil recode dari aplikasi
lama. Target runtime PHP 8.4+, dengan dukungan PHP 8.2+. Project mendukung AI
coding agents untuk perubahan yang terukur, aman, dan mudah diuji.

## Struktur

- index.php: front controller dan security headers.
- app/Config: konfigurasi aplikasi, routing, database, dan debug.
- app/Controllers: controller aplikasi dan action publik.
- app/Models: model aplikasi.
- app/Middleware: middleware route class-based.
- app/Views: template PHP; gunakan e() untuk output dinamis.
- system/Core: kernel MVC, dispatcher, routing, session, input, dan security.
- assets: asset lokal Retro-term dan DebugBar.
- storage: session runtime; jangan menyimpan source code atau data publik.

## Aturan perubahan

1. Baca file target dan pertahankan perubahan user yang sudah ada.
2. Gunakan apply_patch untuk edit manual.
3. Jangan commit password, token, atau kredensial.
4. Jangan menonaktifkan CSRF, escaping, CSP, prepared statements, atau path
   protection tanpa alasan dan pengujian yang jelas.
5. Route baru berada di app/Config/Routes.php.
6. Middleware baru berada di app/Middleware/NameMiddleware.php dan harus
   mengimplementasikan System\Middleware.
7. Session tetap berada di storage/sessions, bukan folder publik.
8. Pertahankan strict types dan hindari dynamic properties deprecated.
9. Jangan menambahkan CDN untuk asset yang sudah tersedia lokal.

## Verifikasi

Jalankan dari root project:

    composer validate --no-check-publish
    find . -path './vendor' -prune -o -name '*.php' -print0 | xargs -0 -n1 php -l

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
- memperbarui README.md jika public API, struktur folder, instalasi, atau
  konfigurasi berubah.

## Definition of done

Source valid, tidak menambah deprecated warning yang diketahui, security
baseline tetap aktif, dokumentasi public API diperbarui, dan hasil verifikasi
dilaporkan.
