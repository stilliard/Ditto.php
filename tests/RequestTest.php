<?php

use PHPUnit\Framework\TestCase;
use Ditto\Request;

class RequestTest extends TestCase
{
	public function testSendRequest()
	{
		$req = new Request('GET', 'https://www.wildfireinternet.co.uk');
		$res = $req->send('/');
		$this->assertStringContainsString('<body', (string)$res->getBody());
	}
}
