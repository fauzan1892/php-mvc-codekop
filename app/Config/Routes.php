<?php

declare(strict_types=1);

namespace Config;

defined('BASEPATH') || exit('No direct script access allowed');

use System\Route;

$routes['DefaultController'] = 'Home::index';

require_once ROOTPATH . 'app/Routes/web.php';
require_once ROOTPATH . 'app/Routes/api.php';

$routes['routes'] = [];
$routes['active'] = true;
