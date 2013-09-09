<?php

namespace Geronimo\Crawler;

abstract class AbstractCrawler {
    protected $httpClient;
    protected $documentFactory;
    protected $urlFilter;
    public $useRobotsFile = true;
    public $crawlStyleSheets = false;
    public $crawlScripts = false;
    public $crawlImages = false;
    public $crawlAnchors = true;
    public $retainDocumentBody = false;
    public $maxRequests = 5000;
    public $cooldown = 100;
    
    
    /**
     *  @param \Geronimo\Http\Client $httpClient - component that will fetch the resource from the web.
     *  @param \Geronimo\DocumentFactory $documentFactory - component that will create the documents from a request
     *  @param Callable $urlFilter call back that when given a url will return false when the crawler should not crawl
     **/
    public function __construct(\Geronimo\Http\Client $httpClient,
                                \Geronimo\DocumentFactory $documentFactory,
                                \Geronimo\UrlFilter $urlFilter)
    {
        $this->httpClient = $httpClient;
        $this->documentFactory = $documentFactory;
        $this->urlFilter = $urlFilter;
    }
    
    protected function processRobots($url)
    {
        // attempt to get the robots file
        $pathParts = parse_url($url);
        
        $robotsUrl = $pathParts["scheme"]."://".$pathParts["host"]."/robots.txt";
        $robots = $this->httpClient->fetchUrl($robotsUrl);
        if ($robots["status_code"] == 200){
            $this->urlFilter->addFilterRule(new \Geronimo\UrlFilter\Robots($robots['body']));    
        }
    }
    
    /**
     * @param string $url Url to start crawling from
     * @return array[/Geronimo/Document] The results of the crawl.
     **/
    public function crawl($url)
    {
        // clear any outstanding list and kick off the crawl
        if ($this->useRobotsFile){
            $this->processRobots($url);
        }
        return $this->doCrawl($url);
    }
    
    abstract protected function doCrawl($start);
    
}
