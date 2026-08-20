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
        body { background:radial-gradient(circle at 50% -10%, rgba(var(--rt-primary-rgb),.1), transparent 30rem), var(--rt-bg); }
        .test-nav { display:flex; align-items:center; justify-content:space-between; gap:1rem; padding:1rem 0; border-bottom:1px solid var(--rt-border); }
        .test-brand { display:flex; align-items:center; gap:.65rem; color:var(--rt-text); text-decoration:none; font-weight:800; }
        .test-brand img { width:38px; height:38px; border-radius:10px; }
        .test-nav button { border-radius:999px; }
        .test-main { min-height:calc(100vh - 150px); display:grid; place-items:center; padding:clamp(3rem,8vw,6rem) 1rem; }
        .test-card { width:min(720px,100%); padding:clamp(1.5rem,5vw,3.5rem); border:1px solid var(--rt-border); border-radius:18px; background:var(--rt-surface); }
        .test-icon { width:48px; height:48px; display:grid; place-items:center; margin-bottom:1.25rem; border:1px solid rgba(var(--rt-primary-rgb),.28); border-radius:50%; background:rgba(var(--rt-primary-rgb),.1); color:var(--rt-primary); }
        .test-icon i { width:1.45rem; height:1.45rem; display:block; background:currentColor; }
        .test-card .rt-badge { border-radius:999px; }
        .test-card h1 { margin-top:1.25rem; letter-spacing:-.035em; }
        .test-copy { max-width:600px; color:var(--rt-muted); line-height:1.75; }
        .test-terminal { margin-top:1.75rem; padding:1rem 1.15rem; border:1px solid var(--rt-border); border-radius:12px; background:rgba(17,24,39,.72); color:#b7f7c0; font:13px/1.8 ui-monospace,SFMono-Regular,Menlo,monospace; overflow:auto; }
        .test-actions { display:flex; flex-wrap:wrap; gap:.75rem; margin-top:1.75rem; }
        .test-actions .rt-btn { border-radius:999px; }
        .test-actions i { width:1.1rem; height:1.1rem; display:inline-block; margin-right:.35rem; background:currentColor; vertical-align:-.15em; }
        .test-footer { padding:1.5rem 1rem 2rem; border-top:1px solid var(--rt-border); color:var(--rt-muted); font-size:.85rem; text-align:center; }
        .test-footer a { color:var(--rt-text); }
        @media (max-width:560px) {
            .test-nav { padding-inline:1rem; }
            .test-nav button span { display:none; }
        }
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

<footer class="test-footer">
    Support by
    <a href="https://aiti-solutions.com/" target="_blank" rel="noopener noreferrer">AITI Solutions</a>
    · <a href="https://www.codekop.com/" target="_blank" rel="noopener noreferrer">Codekop</a>
    · <a href="https://github.com/fauzan1892/php-mvc-codekop" target="_blank" rel="noopener noreferrer">GitHub</a>
</footer>

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
