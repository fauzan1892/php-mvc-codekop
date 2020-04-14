<?php
    require_once 'app/Config/Config.php';
    require_once 'app/Config/Debug.php';
    require_once 'Function.php';

    require_once 'Controller.php';
    require_once 'App.php';
    require_once 'Views.php';
    require_once 'Crud.php';

    require_once 'app/Config/Database.php';
    require_once 'Database.php';

    $app = new App(default_view);

?>