<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

require_once dirname(__FILE__) . '/../vendor/autoload.php';

echo \Ditto\Factory::run(array(
	'proxy_url' => 'http://localhost:8008',
	'domain_url' => 'https://www.postboxshop.com/',
	'start_path' => '/',
	'cookie_file' => '/tmp/somecookie',
));
