## Codekop PHP MVC Version 1.0 ( Beta )
A simple open web project using php mvc frameworks from Codekop 

## Starter Apps :
setting base url on index : app/Config/Config.php

<pre>
 $config['base_url'] = "http://".$_SERVER['HTTP_HOST'].preg_replace('@/+$@','',dirname($_SERVER['SCRIPT_NAME'])).'/';
</pre>

## Default Controller
setting default controller : app/Config/Routes.php
<pre>
$routes['DefaultController'] = 'Home::index';
</pre>

## Setting Database

setting database  : app/Config/Database.php

<pre>
    $dbhost_ = 'localhost'; // host your server
    $dbname_ = ''; // your database name
    $dbuser_ = 'root'; // user your server
    $dbpass_ = '';  // pass your server
</pre>

## Model View Controller
create models in codekop framework on app/Models
create controller in codekop framework on app/Controllers
create views in codekop framework on app/Views :
how to use views in controller example :
<pre>
// controller 
public function index()
{
    $data['title'] = 'Selamat datang di codekop php mvc';
    $this->show->view('welcome_view', $data);
}

</pre>

## Contributors

<a href="https://github.com/fauzan1892" target="_blank"> fauzan1892 Github</a>
<br/>
<a href="https://fauzan.codekop.com/" target="_blank"> Fauzan Portfolio Website</a>
<br/>
<a href="https://www.codekop.com/" target="_blank"> Codekop.com</a>




