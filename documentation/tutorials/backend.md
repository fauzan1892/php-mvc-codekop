# Backend: Queue, CRUD AI, dan RBAC

Dokumen ini berisi pengembangan backend dari awal sampai verifikasi: queue
seperti Laravel, workflow membuat CRUD dengan AI, dan blueprint RBAC.

## Queue backend

Queue memakai job class di `app/Jobs` dan worker CLI di `system/console.php`.
API dibuat sederhana seperti Laravel tanpa dependency queue eksternal.

### Membuat job

Buat `app/Jobs/SendReportJob.php`:

```php
<?php
declare(strict_types=1);

namespace App\Jobs;

use System\Queue\Job;

final class SendReportJob extends Job
{
    public int $tries = 5;
    public int|array $backoff = [10, 30, 60];

    public function __construct(public readonly int $reportId)
    {
    }

    public function handle(): void
    {
        // Proses report berdasarkan $this->reportId.
    }
}
```

Job harus berada di namespace `App\Jobs`, serializable, dan memiliki
`handle(): void`. Jangan memasukkan password, token, closure, atau data sensitif
ke property job.

### Dispatch dan worker

Dispatch dari controller atau service:

```php
use App\Jobs\SendReportJob;
use System\Queue\Queue;

$id = Queue::dispatch(new SendReportJob($reportId));
Queue::dispatch(new SendReportJob($reportId), 'reports', 30);
```

Parameter ketiga adalah delay dalam detik. Queue default menggunakan file
driver di `storage/queue`.

```bash
php system/console.php queue:work
php system/console.php queue:work --queue=reports
php system/console.php queue:work --once
php system/console.php queue:work --sleep=5 --max-jobs=100
```

Worker dijalankan dari CLI atau service manager, bukan endpoint HTTP.

### Retry dan failed job

`tries` menentukan jumlah percobaan. `backoff` menentukan jeda retry:

```php
public int $tries = 3;
public int|array $backoff = [10, 30, 60];

public function failed(Throwable $exception): void
{
    // Catat notifikasi atau audit failure.
}
```

File job gagal berada di `storage/queue/failed`. Database driver menyimpan
failed job di tabel `failed_jobs`.

### Driver database

Untuk multi-worker atau beberapa server, gunakan driver database:

```php
'queue' => [
    'driver' => 'database',
    'default' => 'default',
    'retry_after' => 90,
    'database' => [
        'table' => 'jobs',
        'failed_table' => 'failed_jobs',
        'auto_create' => true,
    ],
],
```

Driver memakai koneksi PDO aplikasi dan mendukung MySQL/MariaDB serta SQLite.

### Verifikasi queue

```bash
composer test
php system/console.php queue:work --once
```

Test queue berada di `tests/Unit/Queue`. Payload queue tidak boleh dapat
diakses HTTP dan `storage/queue` hanya writable oleh user aplikasi/worker.

## Tutorial CRUD dengan Codex atau Claude

Sebelum meminta AI menulis CRUD, minta AI membaca aturan project dan pola kode
yang sudah ada. Ini mencegah route, security, dan gaya kode baru bertabrakan
dengan boilerplate.

### File yang harus dibaca

1. `AGENTS.md`: aturan perubahan, security, verifikasi, dan batasan agent.
2. `README.md`: arsitektur, alur request, konfigurasi, dan cara menjalankan.
3. `composer.json`: versi PHP, autoload PSR-4, dan dependency.
4. `app/Routes/web.php` dan `app/Routes/api.php`: gaya route, method,
   parameter, dan middleware.
5. Controller, model, dan view yang paling mirip.
6. Schema atau migration database: tabel, kolom, index, dan relasi.
7. `system/Core/*` hanya jika fitur menyentuh kernel framework.

### Workflow

1. **Audit:** AI membaca aturan dan file terkait, lalu menyebutkan gap tanpa
   mengubah file.
2. **Kontrak:** tentukan route, field CRUD, validasi, permission, response, dan
   acceptance criteria.
3. **Implementasi:** tambahkan route, controller, model, view, dan migration
   hanya pada scope fitur.
4. **Verifikasi:** jalankan lint, test, smoke test, lalu periksa diff dan
   security regression.

### Prompt CRUD siap pakai

```text
Baca AGENTS.md, README.md, composer.json, app/Routes/web.php,
app/Routes/api.php,
dan file controller/model/view yang paling mirip. Jangan mengubah file dulu.

Buat CRUD untuk resource "products" dengan field:
- name: wajib, string maksimal 150 karakter
- price: wajib, angka >= 0
- active: boolean

Kebutuhan:
- route GET index/create/edit/show dan POST/PUT/DELETE sesuai pola project
- prepared statements dan validasi server-side
- escaping semua output view
- CSRF untuk request yang mengubah data
- permission products.view/create/update/delete
- jangan menambahkan login atau dependency baru

Sebelum selesai: tampilkan file yang diubah, implementasikan perubahan,
jalankan composer test dan lint, lalu laporkan risiko yang tersisa.
```

### Checklist review hasil AI

- Route dan HTTP method sesuai kontrak.
- Input divalidasi server-side dan output view memakai `e()`.
- Query memakai prepared statement; identifier tidak berasal langsung dari input.
- POST, PUT, PATCH, dan DELETE dilindungi CSRF bila berbasis session.
- Permission dicek di server, bukan hanya menyembunyikan tombol di UI.
- PHPUnit, lint, smoke test, dan `git diff` sudah diperiksa.

Panduan agent harus mengikuti [`AGENTS.md`](../../AGENTS.md). Dokumentasi resmi
Codex tentang `AGENTS.md` tersedia di [OpenAI Docs](https://learn.chatgpt.com/docs/agent-configuration/agents-md).

## RBAC blueprint

Boilerplate ini tidak memaksa login atau RBAC. Jika aplikasi membutuhkan
otorisasi, gunakan RBAC di application layer dan hubungkan ke provider
user/session milik aplikasi.

### Model data yang direkomendasikan

- `roles`: `id`, `name`, `slug`; daftar role seperti admin, editor, viewer.
- `permissions`: `id`, `name`, `slug`; hak terkecil seperti
  `products.update`.
- `role_permissions`: `role_id`, `permission_id`; relasi role dan permission.
- `user_roles`: `user_id`, `role_id`; role yang dimiliki user.

### Aturan permission

Gunakan pola `<resource>.<action>`:

```text
products.view
products.create
products.update
products.delete
reports.export
```

Mulai dari permission spesifik. Hindari permission global seperti `is_admin`
untuk semua operasi karena terlalu luas dan sulit diaudit.

### Implementasi policy di server

```php
public function update(string $id): Response
{
    if (!$this->authorization->can('products.update')) {
        return Response::json([
            'error' => ['code' => 'forbidden'],
        ], 403);
    }

    // validasi input, query prepared statement, lalu simpan perubahan
}
```

Aturan wajib:

- **Deny by default:** permission yang belum diberikan harus ditolak.
- **Server-side:** cek permission pada setiap action sensitif.
- **Least privilege:** berikan hanya hak yang diperlukan role.
- **Audit:** catat perubahan role, permission, dan action berisiko.

### Prompt AI untuk RBAC

```text
Baca AGENTS.md dan dokumentasi project terlebih dahulu.
Rancang RBAC untuk resource products dengan role admin, editor, viewer.

Kriteria:
- deny by default
- permission granular: view/create/update/delete
- cek permission di server-side controller/service
- jangan menganggap hidden button sebagai security
- gunakan prepared statements dan audit perubahan permission
- jangan membuat login baru; gunakan user/session provider aplikasi
- tambahkan test untuk allow, deny, dan user tanpa role

Tampilkan schema, file yang diubah, threat model singkat, dan hasil verifikasi.
```
