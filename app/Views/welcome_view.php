<?php
defined('BASEPATH') || exit('No direct script access allowed');
$title = $title ?? 'Codekop MVC';
?>
<!doctype html>
<html lang="id" data-theme="dark" data-radius="flat">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?></title>
    <link rel="icon" href="<?= e(base_url('assets/img/codekop-logo.png')) ?>">
    <link rel="stylesheet" href="<?= e(base_url('assets/retro-term/retro-term.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_url('assets/retro-term/retro-term-icons.min.css')) ?>">
    <style nonce="<?= e(CSP_NONCE) ?>">
        body { background:radial-gradient(circle at 50% -10%, rgba(var(--rt-primary-rgb),.12), transparent 34rem), var(--rt-bg); }
        .codekop-nav { display:flex; align-items:center; justify-content:space-between; gap:1rem; padding:1rem 0; border-bottom:1px solid var(--rt-border); }
        .codekop-brand { display:flex; align-items:center; gap:.65rem; color:var(--rt-text); text-decoration:none; font-size:1rem; font-weight:800; letter-spacing:-.02em; }
        .codekop-brand img { width:38px; height:38px; border-radius:10px; }
        .codekop-theme-toggle { display:inline-flex; align-items:center; gap:.5rem; cursor:pointer; border-radius:999px; }
        .codekop-hero { padding:clamp(4rem,10vw,8rem) 1rem 5rem; }
        .codekop-card { max-width:920px; margin:0 auto; padding:0; }
        .codekop-card .rt-badge { margin-bottom:1.25rem; border-radius:999px; }
        .codekop-card h1 { max-width:760px; margin:0; font-size:clamp(2.35rem,6vw,5rem); line-height:1.04; letter-spacing:-.055em; }
        .codekop-card .rt-hero-subtitle { max-width:670px; margin:1.5rem 0 0; color:var(--rt-muted); font-size:clamp(1rem,2vw,1.2rem); line-height:1.75; }
        .rt-hero-actions { display:flex; flex-wrap:wrap; gap:.75rem; margin-top:2rem; }
        .rt-hero-actions .rt-btn { border-radius:999px; }
        .rt-hero-actions .rt-btn i { width:1.1rem; height:1.1rem; display:inline-block; margin-right:.4rem; background-color:currentColor; vertical-align:-.15em; }
        .rt-badge i { width:1rem; height:1rem; display:inline-block; margin-right:.35rem; background-color:currentColor; vertical-align:-.15em; }
        .codekop-terminal { max-width:650px; margin-top:3.25rem; padding:1rem 1.15rem; border:1px solid var(--rt-border); border-radius:12px; background:rgba(17,24,39,.72); color:#b7f7c0; font:13px/1.8 ui-monospace,SFMono-Regular,Menlo,monospace; }
        .codekop-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin-top:3.5rem; }
        .codekop-feature { min-height:170px; padding:1.25rem; border:1px solid var(--rt-border); border-radius:14px; background:color-mix(in srgb, var(--rt-surface) 72%, transparent); }
        .codekop-feature > i { width:42px; height:42px; display:grid; place-items:center; margin-bottom:1.1rem; border:1px solid rgba(var(--rt-primary-rgb),.28); border-radius:50%; background:rgba(var(--rt-primary-rgb),.1); background-repeat:no-repeat; background-position:center; background-size:1.35rem; color:var(--rt-primary); font-size:1.35rem; }
        .codekop-feature h3 { margin:.25rem 0 .5rem; font-size:1rem; }
        .codekop-feature p { margin:0; color:var(--rt-muted); line-height:1.65; }
        .codekop-footer { padding:1.5rem 1rem 2rem; border-top:1px solid var(--rt-border); color:var(--rt-muted); font-size:.85rem; text-align:center; }
        .codekop-footer a { color:var(--rt-text); }
        @media (max-width:680px) {
            .codekop-nav { padding-inline:1rem; }
            .codekop-theme-toggle span { display:none; }
            .codekop-grid { grid-template-columns:1fr; margin-top:2.5rem; }
            .codekop-feature { min-height:0; }
            .codekop-hero { padding-top:3.5rem; }
        }
    </style>
</head>
<body>
<header class="rt-container codekop-nav">
    <a class="codekop-brand" href="<?= e(base_url()) ?>">
        <img src="<?= e(base_url('assets/img/codekop-logo.png')) ?>" alt="Codekop MVC">
        <span>Codekop MVC</span>
    </a>
    <button class="rt-btn rt-btn-secondary codekop-theme-toggle" id="codekopThemeToggle" type="button" aria-label="Ganti tema">
        <i class="rt rt-moon" aria-hidden="true"></i><span>Dark / Light</span>
    </button>
</header>

<main>
    <section class="codekop-hero">
        <div class="rt-container">
            <div class="codekop-card">
                <div class="rt-badge rt-badge--primary">
                    <i class="rt rt-check-circle" aria-hidden="true"></i>
                    PHP 8.1+ · Native MVC · secure baseline
                </div>
                <h1 class="rt-hero-title">Bangun aplikasi dengan <span class="rt-gradient-text">Retro-term UI</span></h1>
                <p class="rt-hero-subtitle">
                    <?= e($title) ?> dengan controller, routing custom, templating PHP-native,
                    security headers, CSRF protection, dan DebugBar development.
                </p>
                <div class="rt-hero-actions">
                    <a class="rt-btn rt-btn-primary rt-btn-lg" href="<?= e(base_url('home/test')) ?>">
                        <i class="rt rt-terminal" aria-hidden="true"></i> Coba Routing
                    </a>
                    <a class="rt-btn rt-btn-secondary rt-btn-lg" href="https://github.com/fauzan1892/php-mvc-codekop" target="_blank" rel="noopener noreferrer">
                        <i class="rt rt-github" aria-hidden="true"></i> PHP MVC Codekop
                    </a>
                    <a class="rt-btn rt-btn-secondary rt-btn-lg" href="https://github.com/afandisini/Retro-term" target="_blank" rel="noopener noreferrer">
                        <i class="rt rt-github" aria-hidden="true"></i> Retro-term
                    </a>
                </div>
                <div class="codekop-terminal" aria-label="Framework information">
                    <div>$ php -v</div>
                    <div>PHP <?= e(PHP_VERSION) ?> · <?= e(base_url()) ?></div>
                    <div>$ route GET /home/test → Home::test</div>
                </div>
                <div class="codekop-grid">
                    <article class="codekop-feature">
                        <i class="rt rt-shield-check" aria-hidden="true"></i>
                        <h3>Secure by default</h3>
                        <p>CSRF, escaping, secure sessions, CSP, dan prepared statements.</p>
                    </article>
                    <article class="codekop-feature">
                        <i class="rt rt-diagram-3" aria-hidden="true"></i>
                        <h3>Custom MVC routing</h3>
                        <p>Atur route di <code>app/Routes/web.php</code> atau <code>api.php</code>.</p>
                    </article>
                    <article class="codekop-feature">
                        <i class="rt rt-speedometer2" aria-hidden="true"></i>
                        <h3>Developer tooling</h3>
                        <p>DebugBar aktif pada mode development.</p>
                    </article>
                </div>
            </div>
        </div>
    </section>
</main>

<footer class="codekop-footer">
    <div>Page rendered in <?= e(page_rendered()) ?>s · Codekop MVC</div>
    <div>Support by
        <a href="https://aiti-solutions.com/" target="_blank" rel="noopener noreferrer">AITI Solutions</a>
        · <a href="https://www.codekop.com/" target="_blank" rel="noopener noreferrer">Codekop</a>
        · <a href="https://github.com/fauzan1892/php-mvc-codekop" target="_blank" rel="noopener noreferrer">GitHub</a>
    </div>
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
    })();
</script>
</body>
</html>
