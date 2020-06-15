<?php namespace System;
defined('BASEPATH') OR exit('No direct script access allowed');
/*
  |--------------------------------------------------------------------------
  | App Load Controllers from url
  |--------------------------------------------------------------------------
  | 
  | note : can't be scandir for the controler in a new folder on app/Controllers
  |
 */

class App{
    
    protected $controller;

    public function __construct($controller, $active)
    {
        if($active == FALSE)
        {
            $explode_url = explode('/',$_SERVER['REQUEST_URI']);
            $rowurl = count(explode('/',base_url));
            $row_url = count(parse_url(base_url));
            $urlc = $rowurl-$row_url;
            $rurl = $urlc-1;
            $explode_url = array_slice($explode_url, $rurl);
            
            $cx = explode('/', $controller);
            if($cx[0] == $controller)
            {
                $controller_explode = explode('::', $controller);
            }else{
                $controller_explode = explode('::',$cx[1]);
            }

            /*--  include controller default from app/Config/Config.php --*/
            if(empty($explode_url[1]))
            {
                if(file_exists('app/Controllers/'.$controller_explode[0].'.php'))
                {
                    $c = $controller_explode[1];
                    include 'app/Controllers/'.$controller_explode[0].'.php';
                    $object = new $controller_explode[0];
                    $object->$c();

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
                            include 'app/Views/errors/not_found.php';
                        }

                    }else{
                        $object->index();
                    }
                    
                }else{
                    include 'app/Views/errors/not_found.php';
                }
            }
        }else{
            //  i cant of think in this case
        }
    }
}