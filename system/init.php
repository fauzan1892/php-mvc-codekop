<?php
    require_once 'app/Config/Config.php';
    // require_once 'app/Config/Debug.php'; dalam tahap pengembangan
    require_once 'app/Config/Autoload.php';
    
    require_once 'app/Config/Database.php';
    require_once 'Database.php';
    require_once 'Function.php';
    require_once 'Session.php';
    require_once 'Input.php';

    require_once 'Controller.php';
    require_once 'App.php';
    require_once 'Views.php';
    require_once 'Crud.php';



    $app = new App(default_controller);

?>