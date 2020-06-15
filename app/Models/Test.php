<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

use \System\Models;

class Test extends Models {

    public function check()
    {
        echo 'test model';
    }

}