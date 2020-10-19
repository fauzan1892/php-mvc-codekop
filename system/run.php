<?php
    require_once 'system/Config/Config.php';
    require_once 'app/Config/Config.php';
    require_once 'app/Config/Constants.php';

    // avoid direct access
    defined('BASEPATH') OR exit('No direct script access allowed');

    require_once 'app/Config/Routes.php';
    require_once 'app/Config/Debug.php';
    require_once 'app/Config/Autoload.php';
    
    require_once 'Database.php';
    require_once 'app/Config/Database.php';
    
    require_once 'Helper.php';

    require_once 'Core/Session.php';
    require_once 'Core/Models.php';
    require_once 'Core/Input.php';
    require_once 'Core/Controller.php';
    require_once 'Core/App.php';
    require_once 'Core/Views.php';
    require_once 'Core/Crud.php';

    $app = new \System\App($routes['DefaultController'],$routes['active']);

?>