<?php

use PHPUnit\Framework\TestCase;
use Ditto\Response;

class ResponseTest extends TestCase
{

	public function testReplacingHtmlLinkAttributes()
	{
		$html = '
			Nice to talk about /Home.html and not have it replaced...
			Nice to talk about http://www.wildfireinternet.co.uk/ and its ok that it\'s replaced...

			<a href="/Home.html">Home slash</a>
			<a href="Home.html">Home no slash</a>
			<a href=\'Home.html\'>Home single quote</a>
			<a href="http://www.google.co.uk/">External no replace</a>
			<a href="http://www.wildfireinternet.co.uk/">External replace</a>
			<a href="http://www.wildfireinternet.co.uk/pages/beep.html">External replace 2</a>

			<img src="/img/something.png" alt="abc">
			<img src="http://ajax.googleapis.com/something.png">
			
			<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
			<script src="js/demo.js"></script>

			<link rel="stylesheet" href="/css/main.css">

			body {
				background: url(/image/something.png);
				background: url(\'/image/something.png\');
				background: url("/image/something.png");
				background: url("http://ajax.googleapis.com/something.png");
				background: url("https://ajax.googleapis.com/something.png");
				background: url("//ajax.googleapis.com/something.png");
				background: url("http://www.wildfireinternet.co.uk/something.png");
			}
		';
		$expected = '
			Nice to talk about /Home.html and not have it replaced...
			Nice to talk about http://demo.com/proxy/ and its ok that it\'s replaced...

			<a href="http://demo.com/proxy/Home.html">Home slash</a>
			<a href="http://demo.com/proxy/Home.html">Home no slash</a>
			<a href=\'http://demo.com/proxy/Home.html\'>Home single quote</a>
			<a href="http://www.google.co.uk/">External no replace</a>
			<a href="http://demo.com/proxy/">External replace</a>
			<a href="http://demo.com/proxy/pages/beep.html">External replace 2</a>

			<img src="http://demo.com/proxy/img/something.png" alt="abc">
			<img src="http://ajax.googleapis.com/something.png">
			
			<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
			<script src="http://demo.com/proxy/js/demo.js"></script>

			<link rel="stylesheet" href="http://demo.com/proxy/css/main.css">

			body {
				background: url(http://demo.com/proxy/image/something.png);
				background: url(\'http://demo.com/proxy/image/something.png\');
				background: url("http://demo.com/proxy/image/something.png");
				background: url("http://ajax.googleapis.com/something.png");
				background: url("https://ajax.googleapis.com/something.png");
				background: url("//ajax.googleapis.com/something.png");
				background: url("http://demo.com/proxy/something.png");
			}
		';
		$res = new Response($html);
		$res->setProxyPath('http://demo.com/proxy/');
		$res->replaceDomainLinks('http://www.wildfireinternet.co.uk/');
		$res->replaceInternalHtmlLinks();
		$res->replaceInternalCssLinks();
		$actual = $res->getHtml();

		$this->assertEquals($expected, $actual);
	}

}
