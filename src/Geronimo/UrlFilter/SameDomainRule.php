<?php

namespace Geronimo\UrlFilter;

class SameDomainRule implements Rule
{
    private $allowSubdomains = true;
    private $domain; 
    public function __construct($domain)
    {
        $this->domain = parse_url($domain, PHP_URL_HOST);
    }
    
    public function allowSubdomains($allowed){
        $this->allowSubdomains = $allowed;
    }
    public function matches($url)
    {
        if (!$this->allowSubdomains){
            // Hosts must match exactly
            return $this->domain === parse_url($url, PHP_URL_HOST);
        } else {
            
            return preg_match('#'.preg_quote($this->domain).'$#', parse_url($url, PHP_URL_HOST));
        }
    }
}
