<?php 

defined('BASEPATH') or exit('Tidak ada akses skrip langsung diizinkan !');

/*
 *
 * Author  : fauzan1892
 * Website : http://codekop.com/
 * File    : views.php
 * 
 * Description :
 * code functions system for app/config
 * 
 * 
 */
    class CK_Views{

        // untuk include 
        function render_views($viewname,Array $vars = null){
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
            if(file_exists('app/views/'.$viewname.'.php'))
            {
                ob_start();
                include('app/views/'.$viewname.'.php');
                $render = ob_get_clean();
                echo $render;
            }else{
                include('app/config/not_found.php');
            }
        }
    }
?>