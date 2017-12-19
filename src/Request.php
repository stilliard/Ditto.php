<?php

namespace Ditto;

use GuzzleHttp\Client;

class Request
{
	public $method;
	public $url;

	public function __construct($method, $url)
	{
		$this->method = $method;
		$this->url = $url;

		$this->client = new Client(['base_uri' => $this->url]);
	}

	public function send($path)
	{
		return $this->client->request($this->method, $path, [
			'verify' => false,
			'http_errors' => false,
		]);
	}
}
