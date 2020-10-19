<?php
namespace System;
class Config {

    public static $siteURL;
    public static $timeZone;
    
    public function config($params)
    {
        Config::$siteURL = $params['siteURL'];
        Config::$timeZone = $params['timeZone'];
    }
    
    public static function baseURL() {
        return Config::$siteURL;
    }

    public static function timeZone()
    {
        return date_default_timezone_set(Config::$timeZone);
    }

    public static function Helper($helpers = null) {
        foreach ($helpers as $helper)
        { 
            if($helper == '')
            {
                return null;
            }else{
               return include 'app/helper/'.$helper.'.php'; 
            }
        } 
    }

    public static function Model($models = null) {
        foreach ($models as $model)
        { 
            if($model == null)
            {
                return null;
            }else{
                include 'app/Models/'.$model.'.php'; 
                $object = new $model();
                return $object;
            }
        } 
    }
}