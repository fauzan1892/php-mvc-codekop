<?php
/**
 * 
 * Load module
 * 
 */
defined('BASEPATH') or exit('Tidak ada akses skrip langsung diizinkan !');

class Views{

    // untuk include 
    public function view($viewname,Array $vars = null){
        // error $vars = null di hilangkan
        if($vars == null)
        {

        }else{
            if (count($vars)>0){
                foreach($vars as $k=>$v){
                    ${$k}=$v;
                }
            }
        }
        // == we save a copy of the content already existing
        // == at the output buffer (for no interrump it)
        if(file_exists('app/Views/'.$viewname.'.php'))
        {
            ob_start();
            include('app/Views/'.$viewname.'.php');
            $render = ob_get_clean();
            echo $render;
        }else{
            include('app/Views/errors/not_found.php');
        }
    }
}

