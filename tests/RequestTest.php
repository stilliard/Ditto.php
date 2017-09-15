<?php

use Ditto\Request;

class RequestTest extends \PHPUnit_Framework_TestCase
{
	public function testSendRequest()
	{
		$req = new Request('GET', 'https://www.wildfireinternet.co.uk');
		$res = $req->send('/');
		$this->assertContains('<body', (string)$res->getBody());
	}
}
