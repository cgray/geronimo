<?php

namespace Geronimo\Crawler;


class SequencialCrawler implements \Geronimo\Crawler {

    protected $userAgent;
    protected $documentFactory;
    protected $urlFilter;
    protected $crawlStyleSheets = true;
    protected $crawlScripts = true;
    protected $crawlImages = true;
    protected $crawlAnchors = true;
    protected $retainDocumentBody = false;
    protected $maxRequests = 5000;
    
    public function __construct(\Geronimo\Http\ClientInterface $userAgent, \Geronimo\DocumentFactory $documentFactory, Callable $urlFilter){
        $this->userAgent = $userAgent;
        $this->documentFactory = $documentFactory;
        $this->urlFilter = $urlFilter;
    }
    public function crawl($url){
        // clear any outstanding list and kick off the crawl
        $this->processRobots($url);
        return $this->doCrawl($url);
    }
    
    protected function processRobots($url){
        // todo - grab robots.txt and add to url filters where appropriate
    }
    
    protected function doCrawl($start){
        $todoList = [$start];
        $crawledUrls = array();
        $results = array();
        $requestNo = 0;
        while (count($todoList) && ($requestNo < $this->maxRequests)){
            $requestNo++;
            $url = array_shift($todoList);
            echo $url."\n";
            // if the url isnt filtered or previously crawled then fetch it
            $filter = $this->urlFilter;
            if ($filter($url) && !in_array($url, $crawledUrls)){
                $response = $this->userAgent->fetchUrl($url);
                $document = $this->documentFactory->createDocumentFromResponseArray($response);
                if(!in_array($document->getCanonicalUrl(), $crawledUrls)){
                    $crawledUrls[] = $document->getCanonicalUrl();
                    if ($document->getCanonicalUrl() != $url){
                        $crawledUrls[] = $url;
                    }
                    // Add all of the things we care about to the todo list
                    if ($this->crawlAnchors){
                        $this->addUrlsToList($document->getAnchors(), $crawledUrls, $todoList);
                    }
                    if ($this->crawlStyleSheets) {
                        $this->addUrlsToList($document->getStyleSheets(), $crawledUrls, $todoList);
                    }
                    if ($this->crawlImages) {
                        $this->addUrlsToList($document->getImages(), $crawledUrls, $todoList);
                    }
                    if ($this->crawlScripts){
                        $this->addUrlsToList($document->getScripts(), $crawledUrls, $todoList);
                    }

                    // Clear the document body if we said we don't want to keep it.
                    if (!$this->retainDocumentBody){
                        $document->clearBody();
                    }
                    $results[$document->getCanonicalUrl()] = $document;
                }
            }            
        }
        return $results;
    }
    
    private function addUrlsToList($urls, $filterList, &$list){
        foreach($urls as $newUrl){
            if (!in_array($newUrl, $filterList)){
                $list[] = $newUrl;
            }
        }
                                
    }

}
