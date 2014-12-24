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
	}
	
	public function setProxyPath($path)
	{
		$this->proxyPath = $path;
	}
	
	public function replaceDomainLinks($domain)
	{
		$this->html = str_replace($domain, $this->proxyPath, $this->html);
	}

	public function replaceInternalHtmlLinks()
	{
		// replace href & src links where they DO NOT start with https?:... or //...
		$this->html =  preg_replace('/(src|href)=(["\'])(?!((["\'])?https?:|(["\'])?\/\/))\/?/i', '$1=$2' . $this->proxyPath, $this->html);
	}

	public function replaceInternalCssLinks()
	{
		// replace url() links where they DO NOT start with https?:... or //...
		$this->html = preg_replace('/url\((["\'])?(?!((["\'])?https?:|(["\'])?\/\/))\/?/i', 'url($1' . $this->proxyPath, $this->html);
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
