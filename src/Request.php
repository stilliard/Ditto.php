<?php

namespace Ditto;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\FileCookieJar;

class Request
{
	public $method;
	public $url;
	public $cookieJar;

	public function __construct($method, $url, $cookieFile=null)
	{
		$this->method = $method;
		$this->url = $url;
		if ($cookieFile) {
			$this->cookieJar = new FileCookieJar($cookieFile);
		}

		$this->client = new Client(['base_uri' => $this->url]);
	}

	public function send($path)
	{
		return $this->client->request($this->method, $path, [
			'verify' => false,
			'http_errors' => false,
			'cookies' => $this->cookieJar,
		]);
	}
}
