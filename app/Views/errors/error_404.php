<?php defined('BASEPATH') || exit('No direct script access allowed'); ?>
<!doctype html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 · Tidak ditemukan</title>
    <link rel="icon" href="<?= e(base_url('assets/img/codekop-logo.png')) ?>">
    <link rel="stylesheet" href="<?= e(base_url('assets/retro-term/retro-term.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_url('assets/retro-term/retro-term-icons.min.css')) ?>">
    <style nonce="<?= e(CSP_NONCE) ?>">
        body { background:radial-gradient(circle at 50% -10%, rgba(var(--rt-primary-rgb),.1), transparent 30rem), var(--rt-bg); }
        .codekop-error { min-height:100vh; display:grid; place-items:center; padding:1rem; }
        .codekop-error-card { width:min(680px,100%); padding:clamp(1.5rem,5vw,4rem); border:1px solid var(--rt-border); border-radius:18px; background:var(--rt-surface); }
        .codekop-error-brand { display:flex; align-items:center; gap:.65rem; color:var(--rt-text); font-weight:800; text-decoration:none; }
        .codekop-error-brand img { width:38px; height:38px; border-radius:10px; }
        .codekop-error-code { margin:2.25rem 0 .25rem; color:var(--rt-primary); font:900 clamp(5rem,18vw,10rem)/.8 monospace; letter-spacing:-.08em; }
        .codekop-error-title { margin:.75rem 0; }
        .codekop-error-copy { color:var(--rt-muted); line-height:1.7; }
        .codekop-error-actions { display:flex; flex-wrap:wrap; gap:.75rem; margin-top:1.5rem; }
        .codekop-error-actions .rt-btn { border-radius:999px; }
        .codekop-error-actions i { width:1.1rem; height:1.1rem; display:inline-block; margin-right:.35rem; background:currentColor; vertical-align:-.15em; }
        .codekop-error-toggle { float:right; }
        .codekop-error-footer { padding:0 1rem 1.5rem; color:var(--rt-muted); font-size:.85rem; text-align:center; }
    </style>
</head>
<body>
<main class="codekop-error">
    <section class="codekop-error-card">
        <button class="rt-btn rt-btn-secondary codekop-error-toggle" id="codekopThemeToggle" type="button" aria-label="Ganti tema">
            <i class="rt rt-moon" aria-hidden="true"></i>
        </button>
        <a class="codekop-error-brand" href="<?= e(base_url()) ?>">
            <img src="<?= e(base_url('assets/img/codekop-logo.png')) ?>" alt="Codekop MVC">
            <span>Codekop MVC</span>
        </a>
        <div class="codekop-error-code">404</div>
        <h1 class="codekop-error-title">Halaman tidak ditemukan</h1>
        <p class="codekop-error-copy">
            Route yang diminta tidak tersedia atau sudah dipindahkan.
            Periksa URL lalu kembali ke halaman utama.
        </p>
        <div class="codekop-error-actions">
            <a class="rt-btn rt-btn-primary" href="<?= e(base_url()) ?>">
                <i class="rt rt-home" aria-hidden="true"></i> Beranda
            </a>
            <button class="rt-btn rt-btn-secondary" id="codekopBackButton" type="button">
                <i class="rt rt-arrow-left" aria-hidden="true"></i> Kembali
            </button>
        </div>
    </section>
</main>
<footer class="codekop-error-footer">
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
        document.getElementById('codekopThemeToggle')?.addEventListener('click', () => {
            const next = root.dataset.theme === 'dark' ? 'light' : 'dark';
            root.dataset.theme = next;
            localStorage.setItem('codekop-theme', next);
        });
        document.getElementById('codekopBackButton')?.addEventListener('click', () => history.back());
    })();
</script>
</body>
</html>
