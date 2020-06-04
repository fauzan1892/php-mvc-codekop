<?php namespace System;
defined('BASEPATH') OR exit('No direct script access allowed');
/*
  |--------------------------------------------------------------------------
  | Database Settings
  |--------------------------------------------------------------------------
  |
 */

use \PDO;
use Config;

 class Database
{

    public function __construct()
    {
        global $dbconfig;

        $this->host = $dbconfig['hostname'];
        $this->name = $dbconfig['database'];
        $this->user = $dbconfig['username'];
        $this->pass = $dbconfig['password'];

        if($this->name !== '')
        {
            try {
                $this->db = new PDO("mysql:host=$this->host;dbname=$this->name", $this->user, $this->pass);
                $this->db->exec("set names utf8");
                $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $this->db;
            }catch (PDOException $e) {
                echo '<div class="database_erroryzx" style="background:#f58700;">';
                echo 'Connection failed: ' . $e->getMessage();
                echo '<br/><br/> Check in : app/config/Database.php';
                echo '</div>';
            }
        }else{
            return null;
        }
    }

    public function connect()
    {
        if($this->name !== '')
        {
             $this->db;
        }else{
            return null;
        }
    }
}
?>