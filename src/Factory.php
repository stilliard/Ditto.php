<?php

namespace Ditto;

class Factory
{
	/**
	 * Run site through Ditto proxy
	 *
	 * @param array $config
	 * @return string
	 */
	public static function run($config)
	{
		// detect request method and url
		$method = isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD']!=''
					? $_SERVER['REQUEST_METHOD']
					: 'GET';

		$usingUrlParam = false;
		if (isset($config['url_param_access'])) {
			// use a url param to get url we're mimic'ing?
			$url = isset($_GET[$config['url_param_access']]) && $_GET[$config['url_param_access']]!=''
						? $_GET[$config['url_param_access']]
						: '/';
			$parsedDoamin = parse_url($url);
			$domain = (isset($parsedDoamin['scheme']) ? $parsedDoamin['scheme'] : 'http') . '://';
			$domain .= isset($parsedDoamin['host']) ? $parsedDoamin['host'] : '';
			$path = isset($parsedDoamin['path']) ? $parsedDoamin['path'] : '/';
			$path .= isset($parsedDoamin['query']) ? '?' . $parsedDoamin['query'] : '';
			$usingUrlParam = true;
		} else {
			// fallback to given request :)
			$domain = $config['domain_url'];
			$path = isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI']!=''
						? $_SERVER['REQUEST_URI']
						: '/';
		}

		if ($path=='/' && isset($config['start_path']) && $config['start_path']!='') {
			$path = $config['start_path'];
		}

		// parse the proxy url, and grab the directory
		$proxyPath = parse_url($config['proxy_url'], PHP_URL_PATH);

		// replace the proxy directory from the $path
		$path = preg_replace('/^' . preg_quote($proxyPath, '/') . '/', '', $path);

		// TODO: throw an error
		if ($domain == 'http://' || $domain == '') {
			return '';
		}

		// make request
		$req = new Request($method, $domain, isset($config['cookie_file']) ? $config['cookie_file'] : null);
		$res = $req->send($path);

		// setup response
		// give same http status
		self::setHttpStatus($res->getStatusCode());

		// content-type
		$contentType = $res->getHeader('content-type')[0];
		self::setHttpContentType($contentType);

		$content = (string) $res->getBody();

        // Hijack all ajax requests
		if (stristr($contentType, 'html')) {
          	// Idea from: http://verboselogging.com/2010/02/20/hijack-ajax-requests-like-a-terrorist
			$script_include = "
            <script>
            (function(open) {
                // set our start path
            	var ourSuperHackyProxyPath = '" . str_replace('\'', '', $domain) . "/';
                // hijack the XMLHttpRequest open method
                XMLHttpRequest.prototype.open = function(method, url, async, user, pass) {
                    // force internal links to use our proxy
                    if ( ! url.match(/^https?:\/\//)) {
                    	" . ($usingUrlParam
                    		? " url = ourSuperHackyProxyPath + encodeURIComponent(url.replace(/^\//, '')); "
                    		: " url = ourSuperHackyProxyPath + url.replace(/^\//, ''); "
                    	) . "
                    }
                    open.call(this, method, url, async, user, pass);
                };
            })(XMLHttpRequest.prototype.open);
            </script>";
			$content = str_replace('<head>', '<head>' . $script_include, $content);
		}

		// Handle additional HTML content
		if (isset($config['append_html_content']) && stristr($contentType, 'html')) {
			$content .= $config['append_html_content'];
		}

		// run replacements over content
		$res = new Response($content);
		$res->setProxyPath($config['proxy_url'] . (
			$usingUrlParam ? '?' . $config['url_param_access'] . '=' . urlencode($domain)
			: ''
		));
		$res->replaceDomainLinks($domain);
		$res->replaceInternalHtmlLinks($usingUrlParam);
		if ( stristr($path, '.css') ) {
			$res->replaceInternalCssLinks($usingUrlParam);
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
