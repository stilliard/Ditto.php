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

		$this->client = new Client();
	}

	public function send($path)
	{
		$request = $this->client->createRequest($this->method, $this->url . $path);
		return $this->client->send($request);
	}
}
