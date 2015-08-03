<?php

namespace Ditto;

class Factory
{
	public static function run($config)
	{
		// detect request method and url
		$method = isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD']!=''
					? $_SERVER['REQUEST_METHOD']
					: 'GET';
		$path = isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI']!=''
					? $_SERVER['REQUEST_URI']
					: '/';

		if ($path=='/' && isset($config['start_path']) && $config['start_path']!='') {
			$path = $config['start_path'];
		}

		// parse the proxy url, and grab the directory
		$proxyPath = parse_url($config['proxy_url'], PHP_URL_PATH);

		// replace the proxy directory from the $path
		$path = preg_replace('/^' . preg_quote($proxyPath, '/') . '/', '', $path);

		// make request
		$req = new Request($method, $config['domain_url']);
		$res = $req->send($path);

		// setup response
		// give same http status
		self::setHttpStatus($res->getStatusCode());

		// content-type
		self::setHttpContentType($res->getHeader('content-type'));

		$content = (string) $res->getBody();

        // Hijack all ajax requests
		if (stristr($res->getHeader('content-type'), 'html')) {
          	// Idea from: http://verboselogging.com/2010/02/20/hijack-ajax-requests-like-a-terrorist
			$script_include = "
            <script>
            (function(open) {
                // set our start path
            	var ourSuperHackyProxyPath = '" . str_replace('\'', '', $config['proxy_url']) . "/';
                // hijack the XMLHttpRequest open method
                XMLHttpRequest.prototype.open = function(method, url, async, user, pass) {
                    // force internal links to use our proxy
                    if ( ! url.match(/^https?:\/\//)) {
                        url = ourSuperHackyProxyPath + url.replace(/^\//, '');
                    }
                    open.call(this, method, url, async, user, pass);
                };
            })(XMLHttpRequest.prototype.open);
            </script>";
			$content = str_replace('<head>', '<head>' . $script_include, $content);
		}

		// Handle additional HTML content
		if (isset($config['append_html_content']) && stristr($res->getHeader('content-type'), 'html')) {
			$content .= $config['append_html_content'];
		}

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
