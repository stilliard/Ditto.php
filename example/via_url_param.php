<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

require_once dirname(__FILE__) . '/../vendor/autoload.php';

echo \Ditto\Factory::run(array(
	'url_param_access' => 'ditto_domain_url',
	'proxy_url' => 'http://localhost:8008',
));
