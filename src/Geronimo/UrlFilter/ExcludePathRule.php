<?php
namespace Geronimo\UrlFilter;


class ExcludePathRule implements Rule
{
    private $path;
    
    public function __construct($path)
    {
        $this->path = $path;
    }
    public function matches($url)
    {
        $url = parse_url($url, PHP_URL_PATH);
        return !(strpos($url, $this->path) == 0);
    }
}
