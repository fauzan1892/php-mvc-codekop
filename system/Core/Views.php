<?php
declare(strict_types=1);
namespace System;
defined('BASEPATH') OR exit('No direct script access allowed');
/*
  |--------------------------------------------------------------------------
  | Views Settings
  |--------------------------------------------------------------------------
  |
 */
class Views{
    private array $sections = [];
    private ?string $activeSection = null;

    public function e(mixed $value): string { return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
    public function csrf_field(): string { return '<input type="hidden" name="_csrf" value="' . $this->e(Security::token()) . '">'; }
    public function section(string $name): void { $this->activeSection = $name; ob_start(); }
    public function endSection(): void { if ($this->activeSection !== null) { $this->sections[$this->activeSection] = ob_get_clean(); $this->activeSection = null; } }
    public function yield(string $name, string $default = ''): string { return $this->sections[$name] ?? $default; }
    public function layout(string $layout, array $vars = []): void { $this->view($layout, $vars); }

    // untuk include 
    public function view(string $viewname, ?array $vars = null): void {
        // error $vars = null di hilangkan
        // == we save a copy of the content already existing
        // == at the output buffer (for no interrump it)
        $path = ROOTPATH . 'app/Views/' . ltrim($viewname, '/\\') . '.php';
        if(file_exists($path) && str_ends_with($path, '.php'))
        {
            ob_start();
            extract($vars ?? [], EXTR_SKIP);
            include $path;
            $render = ob_get_clean();
            echo $render;
        }else{
            $notFound = ROOTPATH . 'app/Views/errors/error_404.php';
            if (is_file($notFound)) include $notFound;
        }
    }
}
