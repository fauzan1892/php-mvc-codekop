# Codekop PHP MVC Version 1.0 ( Beta )
A simple open web project using php mvc frameworks from Codekop 

# Starter Apps :
setting base url and default controller on index : app/Config/Config.php

<pre>
define('base_url',"http://".$_SERVER['HTTP_HOST'].preg_replace('@/+$@','',dirname($_SERVER['SCRIPT_NAME'])).'/'); 
define('default_view','Home');
</pre>

setting database : app/Config/Database.php

<pre>
    $dbhost = 'localhost'; // host your server
    $dbname = ''; // your database name
    $dbuser = 'root'; // user your server
    $dbpass = '';  // pass your server
</pre>

# Controllers

create controller in codekop framework on app/Controllers

# Contributors

<a href="https://github.com/fauzan1892"> fauzan1892 Github</a>
<br/>
<a href="https://fauzan.codekop.com/"> Fauzan Portfolio Website</a>
<br/>
<a href="https://www.codekop.com/"> Codekop.com</a>




