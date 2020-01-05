<?php
defined('BASEPATH') or exit('Tidak ada akses skrip langsung diizinkan !');

// helper required file $helpers = array('alert','fungsi','url'); any more in app/helper/
$helpers = array('alert');

// for alert foreach  included
foreach ($helpers as $helper){ 
    require 'app/helper/'.$helper.'.php'; 
} 