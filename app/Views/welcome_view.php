<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?= $title;?></title>

	<style type="text/css">

	::selection { background-color: #E13300; color: white; }
	::-moz-selection { background-color: #E13300; color: white; }

	body {
		background-color: #ddd;
		margin: 40px;
        margin-top:100px;
		font: 13px/20px normal Helvetica, Arial, sans-serif;
		color: #4F5155;
	}

	a {
		color: #fff;
		background-color: transparent;
		font-weight: normal;
	}

	h1 {
        color:#fff;
		background-color: transparent;
		border-bottom: 1px solid #D0D0D0;
		font-size: 19px;
		font-weight: normal;
		margin: 0 0 14px 0;
		padding: 14px 15px 10px 15px;
	}

	code {
		font-family: Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 12px;
		background-color: #ddd;
		border-radius:7px 7px 7px 7px;
		border: 1px solid #D0D0D0;
		color: #002166;
		display: block;
		margin: 14px 0 14px 0;
		padding: 12px 10px 12px 10px;
	}

	#body {
		margin: 0 15px 0 15px;
	}

	p.footer {
		text-align: right;
		font-size: 11px;
		border-top: 1px solid #D0D0D0;
		line-height: 32px;
		padding: 0 10px 0 10px;
		margin: 20px 0 0 0;
	}

	#container {
		margin: 10px;
		border: 1px solid #D0D0D0;
		/**box-shadow: 0 0 50px #78acff;**/
		border-radius:7px 7px 7px 7px;
        background:#1d8a60;
        color:#fff;
	}
	</style>
</head>
<body>
<div id="container">
	<h1>Selamat datang di Codekop PHP Framework</h1>

	<div id="body">
		<p>Halaman yang Anda lihat sedang dibuat secara dinamis oleh Bingkai Kerja Web Codekop </p>

		<p>Jika Anda mau mengedit halaman ini dan  include controller, terletak di : </p>
		<code>app/Controllers/Home.php</code>

		<p>Dokumentasi cara penggunaannya nanti dulu ya pantengin aja <a href="https://www.codekop.com/">Codekop.com</a>.</p>
	</div>

	<p class="footer">Page Rendered in <?=page_rendered();?>s - Codekop PHP Framework Version <strong>1.0</strong></p>
</div>
</body>
</html>