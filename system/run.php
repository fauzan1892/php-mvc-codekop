<?php
declare(strict_types=1);

    require_once ROOTPATH . 'system/Config/Config.php';
    require_once ROOTPATH . 'app/Config/Config.php';
    require_once ROOTPATH . 'app/Config/Constants.php';
    require_once ROOTPATH . 'system/Core/Route.php';
    require_once ROOTPATH . 'system/Core/Middleware.php';
    require_once ROOTPATH . 'system/Core/Request.php';
    require_once ROOTPATH . 'system/Core/Response.php';

    // avoid direct access
    defined('BASEPATH') OR exit('No direct script access allowed');

    require_once ROOTPATH . 'app/Config/Routes.php';
    require_once ROOTPATH . 'app/Config/Debug.php';
    require_once ROOTPATH . 'app/Config/Autoload.php';
    
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
    $debugMode = defined('APP_DEBUG') && APP_DEBUG;
    $autoload = ROOTPATH . 'vendor/autoload.php';
    if ($debugMode && is_file($autoload)) {
        require_once $autoload;
        if (class_exists(\DebugBar\StandardDebugBar::class)) {
            $debugBar = new \DebugBar\StandardDebugBar();
            $debugBar->getCollector('time')->startMeasure('application', 'Application');
            ob_start();
        }
    }

    $app = new \System\App($routes['DefaultController'],$routes['active']);

    if ($debugBar !== null) {
        $debugBar->getCollector('time')->stopMeasure('application');
        $html = ob_get_clean();
        $renderer = $debugBar->getJavascriptRenderer(
            base_url('assets/plugins/php-debugbar/dist'),
            ROOTPATH . 'assets/plugins/php-debugbar/dist'
        );
        $renderer->setCspNonce(CSP_NONCE);
        $debugCssPath = ROOTPATH . 'assets/plugins/php-debugbar/dist/debugbar.min.css';
        $debugJsPath = ROOTPATH . 'assets/plugins/php-debugbar/dist/debugbar.min.js';
        $debugAssets = '';
        if (is_file($debugCssPath) && is_file($debugJsPath)) {
            $debugAssets .= '<style nonce="' . CSP_NONCE . '">' . file_get_contents($debugCssPath) . '</style>';
            $debugAssets .= '<script nonce="' . CSP_NONCE . '">' . file_get_contents($debugJsPath) . '</script>';
        } else {
            $debugAssets = $renderer->renderHead();
        }
        $html = str_replace('</head>', $debugAssets . '</head>', $html);
        $html = str_replace('</body>', $renderer->render() . '</body>', $html);
        echo $html;
    }

?>
