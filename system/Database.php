<?php
defined('BASEPATH') or exit('Tidak ada akses skrip langsung diizinkan !');

class Database {

    public function __construct()
    {
        global $dbhost;
        global $dbname;
        global $dbuser;
        global $dbpass;

        $this->host = $dbhost;
        $this->name = $dbname;
        $this->user = $dbuser;
        $this->pass = $dbpass;

        if($this->name !== '')
        {
            try {
                $dbConnection = new PDO("mysql:host=$this->host;dbname=$this->name", $this->user, $this->pass);
                $dbConnection->exec("set names utf8");
                $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $dbConnection;
            }catch (PDOException $e) {
                echo '<style> .database_erroryzx{ width:0 100px;margin: 10px;padding-top:1pc;padding-bottom:1pc;padding-left:2pc;padding-right:2pc;
                    background:#e32b4a;color:#fff;font-size:16px; }</style>';
                echo '<div class="database_erroryzx">';
                echo 'Connection failed: ' . $e->getMessage();
                echo '<br/><br/> Check in : app/config/Database.php';
                echo '</div><br/><br/>';
            }
        }else{
            return null;
        }
    }
}
?>