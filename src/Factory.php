<?php

namespace Ditto;

class Factory
{
	public static function run($config)
	{
		// detect request method and url
		$method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
		$path = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';

		// make request
		$req = new Request($method, $config['domain_url']);
		$res = $req->send($path);

		// setup response
		// give same http status
		self::setHttpStatus($res->getStatusCode());

		// content-type
		self::setHttpContentType($res->getHeader('content-type'));

		// detect any body
		$content = (string)$res->getBody();

		// run replacements over content
		$res = new Response($content);
		$res->setProxyPath($config['proxy_url'].'/');
		$res->replaceDomainLinks($config['domain_url'].'/');
		$res->replaceInternalHtmlLinks();
		if ( stristr($path, '.css') ) {
			$res->replaceInternalCssLinks();
		}
		$content = $res->getHtml();

		// and finaly return the output
		return $content;
	}

	public static function setHttpStatus($status)
	{
		if (function_exists('http_response_code')) {
			http_response_code($status);
		}
		else {
			header('x', true, $status);
		}
	}

	public static function setHttpContentType($contentType)
	{
		header('Content-type: ' . $contentType);
	}
}
