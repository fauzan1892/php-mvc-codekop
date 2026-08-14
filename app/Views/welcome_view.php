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
    <link rel="icon" href="<?= e(base_url('assets/img/logo_retro_term.svg')) ?>">
    <link rel="stylesheet" href="<?= e(base_url('assets/retro-term/retro-term.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_url('assets/retro-term/retro-term-icons.min.css')) ?>">
    <style nonce="<?= e(CSP_NONCE) ?>">
        .codekop-nav { padding:1rem; }
        .codekop-brand { display:flex; align-items:center; gap:.75rem; color:var(--rt-text); text-decoration:none; font-weight:900; }
        .codekop-brand { font-size:1.15rem; letter-spacing:-.02em; }
        .codekop-brand img { width:52px; height:52px; filter:drop-shadow(0 8px 18px rgba(var(--rt-primary-rgb),.25)); }
        .codekop-nav { display:flex; align-items:center; justify-content:space-between; gap:1rem; }
        .codekop-theme-toggle { display:inline-flex; align-items:center; gap:.5rem; cursor:pointer; }
        .codekop-hero { min-height:72vh; display:grid; place-items:center; padding:5rem 1rem 3rem; }
        .codekop-card { padding:clamp(1.5rem, 4vw, 4rem); border:1px solid var(--rt-border); background:var(--rt-surface); }
        .codekop-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:1rem; margin-top:2rem; }
        .codekop-feature { padding:1.25rem; border:1px solid var(--rt-border); background:var(--rt-surface); }
        .codekop-feature > i { width:58px; height:58px; display:inline-block; margin-bottom:1rem; border:1px solid rgba(var(--rt-primary-rgb),.3); background-color:var(--rt-primary); background-repeat:no-repeat; background-position:center; background-size:2rem; color:var(--rt-primary); font-size:2rem; }
        .codekop-feature h3 { margin:.25rem 0 .5rem; }
        .codekop-feature p { margin:0; color:var(--rt-muted); line-height:1.65; }
        .rt-hero-actions .rt-btn i { width:1.25rem; height:1.25rem; display:inline-block; margin-right:.5rem; background-color:currentColor; vertical-align:-.2em; }
        .rt-badge i { width:1rem; height:1rem; display:inline-block; margin-right:.35rem; background-color:currentColor; vertical-align:-.15em; }
        .codekop-terminal { margin-top:2rem; padding:1rem; background:#111827; color:#9effa9; border:1px solid #26354f; font-family:monospace; }
        .codekop-footer { padding:2rem 1rem; text-align:center; color:var(--rt-muted); }
    </style>
</head>
<body>
<header class="rt-container codekop-nav">
    <a class="codekop-brand" href="<?= e(base_url()) ?>">
        <img src="<?= e(base_url('assets/img/logo_retro_term.svg')) ?>" alt="Retro-term">
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
                    PHP 8.4 · MVC · OWASP-ready
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
                    <a class="rt-btn rt-btn-secondary rt-btn-lg" href="https://github.com/afandisini/Retro-term" target="_blank" rel="noopener noreferrer">
                        <i class="rt rt-github" aria-hidden="true"></i> Retro-term
                    </a>
                </div>
                <div class="codekop-terminal" aria-label="Framework information">
                    <div>$ php -v</div>
                    <div>PHP 8.4 · <?= e(base_url()) ?></div>
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
                        <p>Atur route di <code>app/Config/Routes.php</code>.</p>
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
    Page rendered in <?= e(page_rendered()) ?>s · Codekop MVC
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
