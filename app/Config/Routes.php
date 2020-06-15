<?php namespace Config;
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
*/

// default Controller used
$routes['DefaultController'] = 'Home::index';

/**
 * 
 * 
 * use Default Routes, True or False 
 * TRUE : using default routes is active after that your will using custom setting url routes, 
 * and not using default controller,
 * 
 * FALSE : your router is using default controller.
 * 
 */
$routes['active'] = FALSE;