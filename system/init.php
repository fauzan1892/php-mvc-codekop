<?php
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
    require_once 'Session.php';
    require_once 'Input.php';

    require_once 'Controller.php';
    require_once 'App.php';
    require_once 'Views.php';
    require_once 'Crud.php';

    $app = new \System\App($routes['DefaultController']);

?>