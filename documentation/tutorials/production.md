# Production Aman, Security, dan Testing

Gunakan tutorial ini sebelum deployment. Project tidak memakai folder `public/`,
sehingga web server wajib membatasi source dan eksekusi PHP melalui konfigurasi
yang sudah disediakan.

## Security baseline

- Front controller memasang security headers dan CSP secara default.
- Script inline memakai nonce; koneksi CSP dibatasi ke origin sendiri.
- Gunakan `csrf_field()` atau header `X-CSRF-TOKEN` untuk mutation.
- Gunakan prepared statement dan validasi identifier melalui CRUD.
- Session memakai strict mode, cookie `HttpOnly`, dan `SameSite`.
- Akses langsung ke `app/`, `system/`, `storage/`, `vendor/`, `tests/`, file
  konfigurasi, dan dotfile diblokir oleh Apache/Nginx.
- Hanya `index.php` yang boleh dieksekusi sebagai PHP melalui web server.

Contoh route yang mengubah data:

```php
Route::group(['middleware' => ['csrf']], static function (): void {
    Route::post('/profile', 'Profile@update');
});
```

Jangan menaruh upload executable di `assets/` atau `storage/`. Permission
write hanya untuk `storage/`; source, dependency, dan konfigurasi tetap read-only
untuk user web bila memungkinkan.

## 1. Checklist sebelum deploy

- Gunakan PHP 8.4 yang direkomendasikan dan Composer production install.
- Ubah `env` menjadi `production`.
- Jangan commit password, JWT secret, API key, atau file `.env`.
- Gunakan database user non-root dengan privilege minimum.
- Pastikan HTTPS, backup, log rotation, dan monitoring tersedia.

```bash
composer install --no-dev --prefer-dist --optimize-autoloader
php -r 'echo PHP_VERSION, PHP_EOL;'
composer validate --no-check-publish
```

## 2. Konfigurasi aplikasi

Production harus menyembunyikan detail exception dan mematikan DebugBar:

```php
// app/Config/Config.php
'env' => 'production',
```

Secret deployment diambil dari environment atau secret manager server. Jangan
menaruh nilai secret langsung di repository:

```php
'api' => [
    'jwt_secret' => getenv('CODEKOP_JWT_SECRET') ?: '',
],
```

Secret kosong harus dianggap konfigurasi invalid untuk endpoint JWT. Pastikan
environment tersedia sebelum service menerima traffic.

## 3. Permission filesystem

Source harus dapat dibaca PHP-FPM, tetapi hanya `storage/` yang boleh ditulis
oleh user web/worker.

```bash
sudo chown -R deploy:www-data /var/www/php-mvc-codekop
sudo find /var/www/php-mvc-codekop -type d -exec chmod 750 {} \;
sudo find /var/www/php-mvc-codekop -type f -exec chmod 640 {} \;
sudo find /var/www/php-mvc-codekop/storage -type d -exec chmod 750 {} \;
sudo find /var/www/php-mvc-codekop/storage -type f -exec chmod 640 {} \;
```

Sesuaikan user/group dengan distro dan konfigurasi PHP-FPM Anda.

## 4. Apache2

Gunakan [`apache2.conf.example`](../../apache2.conf.example). `AllowOverride All`
wajib aktif agar `.htaccess` membaca deny rule dan rewrite.

```bash
sudo a2enmod rewrite headers
sudo a2ensite codekop.conf
sudo apachectl configtest
sudo systemctl reload apache2
```

`.htaccess` memblokir `app`, `system`, `storage`, `vendor`, dan `tests`. Hanya
`index.php` yang boleh dieksekusi.

## 5. Nginx + PHP-FPM

Gunakan [`nginx.conf.example`](../../nginx.conf.example). Location exact hanya
meneruskan `/index.php`; URL PHP lain menghasilkan `404`.

```bash
sudo nginx -t
sudo systemctl reload nginx
```

Jangan menambahkan blok generik `location ~ \.php$` yang meneruskan semua file
PHP ke PHP-FPM.

## 6. Queue worker

Worker dijalankan sebagai service CLI, bukan melalui request web. Untuk file
driver, worker harus memiliki akses tulis ke storage queue.

```bash
php system/console.php queue:work --sleep=3
```

Contoh service systemd:

```ini
[Unit]
Description=Codekop Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/php-mvc-codekop
ExecStart=/usr/bin/php system/console.php queue:work --sleep=3
Restart=always
RestartSec=5
NoNewPrivileges=true
PrivateTmp=true

[Install]
WantedBy=multi-user.target
```

Jika memakai banyak worker atau beberapa server, pertimbangkan driver database
dan pantau failed jobs serta kapasitas queue.

## 7. Verifikasi release

```bash
composer validate --no-check-publish
composer test
find . -path './vendor' -prune -o -name '*.php' -print0 | xargs -0 -n1 php -l
BASE_URL=https://app.example.com bash tests/Security/security_smoke.sh
```

Smoke test harus melalui Apache/Nginx aktif. `php -S` tidak membaca `.htaccess`
dan tidak mewakili konfigurasi production.

## 8. PHPUnit dan security smoke test

PHPUnit dipakai untuk unit/feature test, sedangkan smoke test HTTP memverifikasi
aturan akses server (`.htaccess`, `nginx.conf.example`, dan
`apache2.conf.example`).

```bash
composer install
composer test
BASE_URL=https://app.example.com bash tests/Security/security_smoke.sh
```

Smoke test harus mengecek bahwa path sensitif (`app/`, `system/`, `storage/`,
`vendor/`, `.env`, `composer.json`, dan lainnya) mengembalikan `403`/`404`,
sedangkan `index.php` tetap dapat diakses. Script mengeluarkan `PASS`/`FAIL`
dan exit code non-zero bila ada kegagalan.

Test baru mengikuti namespace dan path, misalnya
`tests/Unit/Queue/QueueManagerTest.php` memakai namespace
`Tests\Unit\Queue`. Gunakan `sys_get_temp_dir()` untuk fixture agar test tidak
menulis ke `storage/` project; bersihkan fixture di `tearDown()`.

Lint seluruh PHP:

```bash
find . -path './vendor' -prune -o -name '*.php' -print0 | xargs -0 -n1 php -l
```

Sebelum PR atau deploy, jalankan `composer validate --no-check-publish`,
`composer test`, lint, security smoke test melalui web server aktif, dan review
`git diff`.

## 9. Operasional

- Rotasi dan simpan log secara aman; jangan menampilkan stack trace ke client.
- Backup database, konfigurasi deployment, dan storage yang diperlukan.
- Pantau HTTP 5xx, error log, failed jobs, queue depth, CPU, memory, dan disk.
- Review dependency Composer dan patch keamanan secara berkala.
- Uji restore backup, bukan hanya membuat backup.
