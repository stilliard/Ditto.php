<?php

namespace Ditto;

use GuzzleHttp\Client;

class Request
{
	public $method;
	public $url;
    protected $headers = [
        'Accept' => '*/*',
        'Accept-Language' => 'en-GB,en;q=0.5',
        'User-Agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:59.0) Gecko/20100101 Firefox/59.0',
    ];

	public function __construct($method, $url)
	{
		$this->method = $method;
		$this->url = $url;

		$this->client = new Client(['base_uri' => $this->url]);
    }

    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }

	public function send($path)
	{
		return $this->client->request($this->method, $path, [
            'headers' => $this->headers,
			'verify' => false,
			'http_errors' => false,
			'cookies' => false,
		]);
	}
}
