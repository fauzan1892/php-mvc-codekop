<?php
defined('BASEPATH') || exit('No direct script access allowed');
$title = $title ?? 'Testing';
$pageTitle = $page_title ?? 'Program testing';
?>
<!doctype html>
<html lang="id" data-theme="dark" data-radius="flat">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> · Codekop MVC</title>
    <link rel="icon" href="<?= e(base_url('assets/img/codekop-logo.png')) ?>">
    <link rel="stylesheet" href="<?= e(base_url('assets/retro-term/retro-term.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_url('assets/retro-term/retro-term-icons.min.css')) ?>">
    <style nonce="<?= e(CSP_NONCE) ?>">
        .test-nav { display:flex; align-items:center; justify-content:space-between; gap:1rem; padding:1rem 0; }
        .test-brand { display:flex; align-items:center; gap:.7rem; color:var(--rt-text); text-decoration:none; font-weight:900; }
        .test-brand img { width:46px; height:46px; }
        .test-main { min-height:calc(100vh - 90px); display:grid; place-items:center; padding:2rem 1rem; }
        .test-card { width:min(820px,100%); padding:clamp(1.5rem,5vw,4rem); border:1px solid var(--rt-border); background:var(--rt-surface); }
        .test-icon { width:64px; height:64px; display:grid; place-items:center; margin-bottom:1.25rem; border-radius:14px; background:var(--rt-primary); color:var(--rt-primary-contrast); font-size:2rem; }
        .test-icon i { width:2rem; height:2rem; display:block; background:currentColor; }
        .test-copy { color:var(--rt-muted); line-height:1.7; }
        .test-terminal { margin-top:1.5rem; padding:1.25rem; border:1px solid #26354f; background:#111827; color:#9effa9; font:14px/1.8 ui-monospace,SFMono-Regular,monospace; overflow:auto; }
        .test-actions { display:flex; flex-wrap:wrap; gap:.75rem; margin-top:1.5rem; }
        .test-actions i { width:1.1rem; height:1.1rem; display:inline-block; margin-right:.35rem; background:currentColor; vertical-align:-.15em; }
    </style>
</head>
<body>
<header class="rt-container test-nav">
    <a class="test-brand" href="<?= e(base_url()) ?>">
        <img src="<?= e(base_url('assets/img/codekop-logo.png')) ?>" alt="Codekop MVC">
        <span>Codekop MVC</span>
    </a>
    <button class="rt-btn rt-btn-secondary" id="testThemeToggle" type="button" aria-label="Ganti tema">
        <i class="rt rt-moon" aria-hidden="true"></i>
        <span>Dark / Light</span>
    </button>
</header>

<main class="test-main">
    <section class="test-card">
        <div class="test-icon">
            <i class="rt rt-terminal" aria-hidden="true"></i>
        </div>
        <div class="rt-badge rt-badge--success">
            <i class="rt rt-check-circle" aria-hidden="true"></i>
            Route berhasil dijalankan
        </div>
        <h1><?= e($pageTitle) ?></h1>
        <p class="test-copy">
            Halaman ini membuktikan controller, routing manual, view loader,
            templating PHP-native, asset lokal, dan security policy berjalan.
        </p>
        <div class="test-terminal" aria-label="Informasi route">
            <div>$ route GET /home/test</div>
            <div>→ Home::test</div>
            <div>$ template</div>
            <div>→ app/Views/tes_view.php · Retro-term</div>
            <div>$ status</div>
            <div>→ OK</div>
        </div>
        <div class="test-actions">
            <a class="rt-btn rt-btn-primary" href="<?= e(base_url()) ?>">
                <i class="rt rt-home" aria-hidden="true"></i> Beranda
            </a>
            <a class="rt-btn rt-btn-secondary" href="<?= e(base_url('api/health')) ?>">
                <i class="rt rt-braces" aria-hidden="true"></i> Coba API
            </a>
        </div>
    </section>
</main>

<script src="<?= e(base_url('assets/retro-term/retro-term.min.js')) ?>" defer></script>
<script nonce="<?= e(CSP_NONCE) ?>">
    (() => {
        const root = document.documentElement;
        const saved = localStorage.getItem('codekop-theme');
        if (saved === 'light' || saved === 'dark') root.dataset.theme = saved;
        document.getElementById('testThemeToggle')?.addEventListener('click', () => {
            const next = root.dataset.theme === 'dark' ? 'light' : 'dark';
            root.dataset.theme = next;
            localStorage.setItem('codekop-theme', next);
        });
    })();
</script>
</body>
</html>
