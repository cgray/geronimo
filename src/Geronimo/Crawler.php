<?php
namespace Geronimo;

interface Crawler
{
    /**
     *  Starts a crawling
     *
     *  @param string $uri Fully Qualified Url
     *  @return DocumentList
     *  
     **/
    
    public function crawl($uri);   
}