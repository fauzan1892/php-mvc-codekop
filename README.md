# codekopv1
Codekop PHP Framework MVC
setting base url and default controller in index : app/config/config.php

    define('base_url',"http://".$_SERVER['HTTP_HOST'].preg_replace('@/+$@','',dirname($_SERVER['SCRIPT_NAME'])).'/'); 
    define('default_view','welcome');

setting database : app/config/database.php

    $dbhost = 'localhost'; // host your server
    $dbname = ''; // your database name
    $dbuser = 'root'; // user your server
    $dbpass = '';  // pass your server
    $dbcharset = 'utf8'; // default  

create controller in codekop framework on app/controller


create helper in codekop framework on app/helper and setting helper in app/config/autoload.php :

   $helpers = array('alert');


