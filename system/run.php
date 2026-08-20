<?php
declare(strict_types=1);

    require_once ROOTPATH . 'system/Config/Config.php';
    require_once ROOTPATH . 'app/Config/Config.php';
    require_once ROOTPATH . 'app/Config/Constants.php';
    require_once ROOTPATH . 'system/Core/Route.php';
    require_once ROOTPATH . 'system/Core/RouteGroup.php';
    require_once ROOTPATH . 'system/Core/Middleware.php';
    require_once ROOTPATH . 'system/Core/Request.php';
    require_once ROOTPATH . 'system/Core/Response.php';

    // avoid direct access
    defined('BASEPATH') OR exit('No direct script access allowed');

    // PSR-4-style application autoloader. This keeps namespaced controllers
    // and models usable in subfolders even when the framework is booted
    // without Composer (for example from a small CLI smoke test).
    spl_autoload_register(static function (string $class): void {
        $prefix = 'App\\';
        if (!str_starts_with($class, $prefix)) return;
        $relative = substr($class, strlen($prefix));
        $file = ROOTPATH . 'app/' . str_replace('\\', '/', $relative) . '.php';
        if (is_file($file)) require_once $file;
    });

    require_once ROOTPATH . 'app/Config/Routes.php';
    require_once ROOTPATH . 'app/Config/Debug.php';
    require_once ROOTPATH . 'app/Config/Autoload.php';

    $autoload = ROOTPATH . 'vendor/autoload.php';
    if (is_file($autoload)) {
        require_once $autoload;
    }
    
    require_once ROOTPATH . 'system/Database.php';
    require_once ROOTPATH . 'app/Config/Database.php';
    
    require_once ROOTPATH . 'system/Helper.php';

    require_once ROOTPATH . 'system/Core/Session.php';
    require_once ROOTPATH . 'system/Core/Models.php';
    require_once ROOTPATH . 'system/Core/Input.php';
    require_once ROOTPATH . 'system/Core/Security.php';
    require_once ROOTPATH . 'system/Core/Controller.php';
    require_once ROOTPATH . 'system/Core/App.php';
    require_once ROOTPATH . 'system/Core/Views.php';
    require_once ROOTPATH . 'system/Core/Crud.php';

    $debugBar = null;
    // DebugBar v3 requires PHP 8.2+. PHP 8.1 should use only the native
    // diagnostics from app/Config/Debug.php when this bootstrap is reused.
    $debugMode = defined('APP_DEBUG') && APP_DEBUG && PHP_VERSION_ID >= 80200;
    if ($debugMode && is_file($autoload)) {
        if (class_exists(\DebugBar\StandardDebugBar::class)) {
            $debugBar = new \DebugBar\StandardDebugBar();
            $debugBar->getCollector('time')->startMeasure('application', 'Application');

            // Use the same PDO instance as System\Database. The collector
            // installs a traceable statement class before controllers/models
            // execute their queries.
            $debugPdo = (new \System\Database())->connect();
            if ($debugPdo instanceof \PDO
                && class_exists(\DebugBar\DataCollector\PDO\PDOCollector::class)) {
                $pdoCollector = new \DebugBar\DataCollector\PDO\PDOCollector($debugPdo);
                $pdoCollector->setTimeDataCollector($debugBar->getCollector('time'));
                $debugBar->addCollector($pdoCollector);
            }
            ob_start();
        }
    }

    $app = new \System\App($routes['DefaultController'],$routes['active']);

    if ($debugBar !== null) {
        $debugBar->getCollector('time')->stopMeasure('application');
        $html = ob_get_clean();
        $renderer = $debugBar->getJavascriptRenderer(
            base_url('assets/plugins/php-debugbar'),
            ROOTPATH . 'assets/plugins/php-debugbar'
        );
        $renderer->setCspNonce(CSP_NONCE);
        $debugAssets = $renderer->renderHead();
        $html = str_replace('</head>', $debugAssets . '</head>', $html);
        $html = str_replace('</body>', $renderer->render() . '</body>', $html);
        echo $html;
    }

?>
