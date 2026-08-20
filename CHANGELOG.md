# Changelog

Semua perubahan penting pada project ini dicatat di file ini.

## [Unreleased]

### Added

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

### Security

- Menambahkan `Cross-Origin-Opener-Policy`.
- Menambahkan `Cross-Origin-Resource-Policy`.
- Menambahkan `X-Permitted-Cross-Domain-Policies`.
- Memperketat CSP dengan `connect-src 'self'`.

### Changed

- Menggunakan logo baru pada welcome view, test view, halaman 404, dan
  dokumentasi.
- Menambahkan PSR-4 autoload untuk namespace `App\\`.
- Merapikan file framework yang disentuh agar mengikuti PSR-12.
- Memperbarui dokumentasi routing dan security di `README.md`.

### Verification

- `composer validate --no-check-publish` berhasil, dengan warning license belum
  ditentukan.
- Seluruh file PHP berhasil melewati `php -l`.
- Smoke test `/api/health`, route parameter, route group, dan resource route
  berhasil.
