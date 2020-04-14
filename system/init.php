<?php
defined('BASEPATH') or exit('Tidak ada akses skrip langsung diizinkan !');
    require_once 'app/Config/Config.php';

    require_once 'Controller.php';
    require_once 'App.php';
    require_once 'Views.php';
    require_once 'Crud.php';

    require_once 'app/Config/Database.php';
    require_once 'Database.php';

    $app = new App(default_view);

?>