<?php
/*
  |--------------------------------------------------------------------------
  | App Load Controllers from url
  |--------------------------------------------------------------------------
  | 
  | note : can't be scandir for the controler in a new folder on app/Controllers
  |
 */
defined('BASEPATH') or exit('Tidak ada akses skrip langsung diizinkan !');

class App{
    
    protected $controller;

    public function __construct($controller)
    {

        $explode_url = explode('/',$_SERVER['REQUEST_URI']);
        $explode_url = array_slice($explode_url, 2);
        
        /*--  include controller default from app/Config/Config.php --*/
        if(empty($explode_url[1]))
        {
            if(file_exists('app/Controllers/'.$controller.'.php'))
            {
                include 'app/Controllers/'.$controller.'.php';
                $object = new $controller();
                $object->index();

            }else{
                include 'app/Views/errors/not_found.php';
            }
        }else{
            if(file_exists('app/Controllers/'.ucfirst($explode_url[1]).'.php'))
            {
                include 'app/Controllers/'.ucfirst($explode_url[1]).'.php';
                $class = $explode_url[1];
                $object = new $class();

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
                include 'app/Views/errors/not_found.php';
            }
        }
    }
}