<?php

namespace Ditto;

class Response
{
	public $origHtml;
	public $html;
	public $proxyPath;

	public function __construct($html)
	{
		$this->html = $this->origHtml = $html;

		// Fix links that don't use quotes
		$this->html = preg_replace_callback('/(src|href)=([^"\']*?)(\s|>)/', function ($matches) {
			return $matches[1] . '="' . $matches[2] . '"' . $matches[3];
		}, $this->html);
	}

	public function setProxyPath($path)
	{
		$this->proxyPath = $path;
	}

	public function replaceDomainLinks($domain)
	{
		$this->html = str_replace($domain, $this->proxyPath, $this->html);
	}

	public function replaceInternalHtmlLinks($usingUrlParam=false)
	{
		// replace href & src links where they DO NOT start with https?:... or //...
		$this->html = preg_replace_callback('/(src|href)=(["\'])(?!((["\'])?https?:|(["\'])?\/\/))(.*?)\2/i', function ($matches) use ($usingUrlParam) {
            return $matches[1] . '=' . $matches[2]
                . rtrim($this->proxyPath, '/')
                . (
					$usingUrlParam
						? urlencode('/' . ltrim(htmlspecialchars_decode($matches[6]), '/')) # url encode when using url param method
						: '/' . ltrim($matches[6], '/')
				)
                . $matches[2];
        }, $this->html);
	}

	public function replaceInternalCssLinks($usingUrlParam=false)
	{
		// replace url() links where they DO NOT start with https?:... or //...
		$this->html = preg_replace_callback('/url\((["\'])?(?!((["\'])?https?:|(["\'])?\/\/))(.*?)(["\'])?\)/i', function ($matches) use ($usingUrlParam) {
			return 'url(' . $matches[1] . rtrim($this->proxyPath, '/') . '/'
				. (
					$usingUrlParam
						? urlencode(ltrim(htmlspecialchars_decode($matches[5]), '/')) # url encode when using url param method
						: ltrim($matches[5], '/')
				)
				. $matches[1] . ')';
		}, $this->html);
	}

	public function getHtml()
	{
		return $this->html;
	}

	public function getOrigHtml()
	{
		return $this->origHtml;
	}
}
