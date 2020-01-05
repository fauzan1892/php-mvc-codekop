<?php
/**
 * Penulis : fauzan1892  
 * E-mail  : fauzan1892@codekop.com
 * 
 * website : https://codekop.com/
 * 
 * 
 * 
 * 
 */

 require 'app/config/config.php';
 require 'app/config/database.php';
 require 'app/config/crud.php';
 require 'app/config/views.php';
 require 'app/config/security.php';
 require 'app/config/fungsi.php';
 require 'app/config/router.php';
 require 'app/config/autoload.php';


 $crud = new prosesCrud($koneksi);
 $lihat = new CK_Views();

 $router = new Router($_SERVER);
    session_on(); 

    ### proses views file di app/controller/nama_file.php ###

    $explode_url = explode('/',$_SERVER['REQUEST_URI']);
    $explode_url = array_slice($explode_url, 2);
    if(empty($_SERVER['PATH_INFO']))
    {
        if(file_exists('app/controller/'.default_view.'.php'))
        {
            include 'app/controller/'.default_view.'.php';
            $class = default_view;
            $object = new $class($lihat,$koneksi);
            $object->index();

        }else{
            include 'app/config/not_found.php';
        }

    }else{
        
        if(file_exists('app/controller/'.$explode_url[1].'.php'))
        {
            include 'app/controller/'.$explode_url[1].'.php';
            $class = $explode_url[1];
            $object = new $class($lihat,$koneksi);

            if(!empty($explode_url[2]))
            {
                $get = explode('?',$explode_url[2]);
                $func = $get[0];
                if ((int)method_exists($class,$func) == '1') {
                    $object->$func();
                } else {
                    include 'app/config/not_found.php';
                }

            }else{
                $object->index();
            }
            
        }else{
            include 'app/config/not_found.php';
        }
    }


$router->run();


?>

