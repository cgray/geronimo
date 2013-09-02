<?php

namespace Geronimo\UrlFilter;

class SameDomainRule implements Rule
{
    private $allowSubdomains;
    private $domain; 
    public function __construct($domain, $allowSubdomains = true)
    {
        $this->allowSubdomains = $allowSubdomains;
        $this->domain = parse_url($domain, PHP_URL_HOST);
    }
    public function matches($url)
    {
        if (!$this->allowSubdomains){
            return $this->domain === parse_url($url, PHP_URL_HOST);
        } else {
            return preg_match('#'.addslashes($this->domain).'$#', parse_url($url, PHP_URL_HOST));
        }
    }
}
