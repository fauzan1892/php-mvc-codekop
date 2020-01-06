<?php
    defined('BASEPATH') or exit('Tidak ada akses skrip langsung diizinkan !');

    $dbhost = 'localhost'; // host your server
    $dbname = ''; // your database name
    $dbuser = 'root'; // user your server
    $dbpass = '';  // pass your server
    $dbcharset = 'utf8'; // default   

    
    if($dbname !== '')
    {
        try {
            $dbConnection = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
            $dbConnection->exec("set names utf8");
            $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch (PDOException $e) {
            echo '<style> .database_erroryzx{ width:0 100px;padding-top:1pc;padding-bottom:1pc;padding-left:2pc;padding-right:2pc;
                background:#e32b4a;color:#fff;font-size:16px; }</style>';
            echo '<div class="database_erroryzx">';
            echo 'Connection failed: ' . $e->getMessage();
            echo '<br/><br/> Check in : app/config/database.php';
            echo '</div><br/><br/>';
        }

        $koneksi = $dbConnection;
        
    }else{
        $koneksi = null;
    }

?>