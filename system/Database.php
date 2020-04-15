<?php
defined('BASEPATH') or exit('Tidak ada akses skrip langsung diizinkan !');

class Database {

    public function __construct()
    {
        global $dbhost_;
        global $dbname_;
        global $dbuser_;
        global $dbpass_;

        $this->host = $dbhost_;
        $this->name = $dbname_;
        $this->user = $dbuser_;
        $this->pass = $dbpass_;

        if($this->name !== '')
        {
            try {
                $dbConnection = new PDO("mysql:host=$this->host;dbname=$this->name", $this->user, $this->pass);
                $dbConnection->exec("set names utf8");
                $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $dbConnection;
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
}
?>