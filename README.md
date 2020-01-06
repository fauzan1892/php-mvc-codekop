# codekopv1
Codekop PHP Framework MVC
setting base url and default controller in index : app/config/config.php

<?php
   define('base_url',"http://".$_SERVER['HTTP_HOST'].preg_replace('@/+$@','',dirname($_SERVER['SCRIPT_NAME'])).'/'); 
   define('default_view','welcome');
?>

setting database : app/config/database.php

    $dbhost = 'localhost'; // host your server
    $dbname = ''; // your database name
    $dbuser = 'root'; // user your server
    $dbpass = '';  // pass your server
    $dbcharset = 'utf8'; // default   

create controller in codekop framework
<?php
    defined('BASEPATH') or exit('Tidak ada akses skrip langsung diizinkan !');

    class welcome{
        
        protected $lihat;
        protected $db;
        function __construct($lihat,$db){
            $this->view = $lihat;
            $this->db = $db;
            session_on(); 
        }

        public function index()
        {
            $this->view->render_views('welcome');
        }
    }
?>

