<?php
defined('BASEPATH') OR exit('No direct script access allowed');

return array(

	'config' => array(
		'base_url' => 'http://localhost:1280/',
		'log_threshold' => 1,
		'index_page' => 'index.php',
		'uri_protocol' => 'REQUEST_URI',
	),

	'database' => array(
		'hostname' => 'db',
		'port' => '3306',
		'username' => 'root',
		'password' => 'secret',
		'database' => 'classroombookings',
		'dbdriver' => 'mysqli',
	),

);
