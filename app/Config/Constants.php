<?php

/*
|--------------------------------------------------------------------------
| This defines the default of Config 
|--------------------------------------------------------------------------
|
|
| this is url of base_url 
*/

define('base_url', $config['base_url']);

/*
|--------------------------------------------------------------------------
| This is default basepath directory
|--------------------------------------------------------------------------
|
|
*/
define('BASEPATH', base_url);

// avoid direct access
defined('BASEPATH') OR exit('No direct script access allowed');
