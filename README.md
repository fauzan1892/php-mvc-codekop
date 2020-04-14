# Codekop PHP MVC Versi 1.0
 open web project using php mvc from Codekop 

# Starter Apps :
setting base url and default controller on index : app/Config/Config.php

<pre>
define('base_url',"http://".$_SERVER['HTTP_HOST'].preg_replace('@/+$@','',dirname($_SERVER['SCRIPT_NAME'])).'/'); 
define('default_view','Home');
</pre>

setting database : app/Config/Database.php

<pre>
<?php
    $dbhost = 'localhost'; // host your server
    $dbname = ''; // your database name
    $dbuser = 'root'; // user your server
    $dbpass = '';  // pass your server
    $dbcharset = 'utf8'; // default  
</pre>

# Controllers
create controller in codekop framework on app/Controllers



