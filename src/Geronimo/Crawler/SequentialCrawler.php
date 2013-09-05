<?php

namespace Geronimo\Crawler;
/**
 * Implements a Syncronous Strategry for Crawling a website.
 **/


class SequentialCrawler extends \Geronimo\Crawler\AbstractCrawler implements \Geronimo\Crawler
{
    public $maxRequests = 5000;
    public $cooldown = 100;
        
    /**
     * Do the actual crawling
     *
     * @param string $start Url to start crawling from.
     **/
    protected function doCrawl($start)
    {
        $todoList = [$start];
        $crawledUrls = array();
        $results = array();
        $requestNo = 0;
        while (count($todoList) && ($requestNo < $this->maxRequests)){
            $requestNo++;
            $url = array_shift($todoList);
             // if the url isnt filtered or previously crawled then fetch it
            $hashPos = strpos($url, "#");
            if ($hashPos != FALSE){
                $url = substr($url, 0 , $hashPos);
            }
            if ($this->urlFilter->isAllowed($url) && !in_array($url, $crawledUrls)){
                $response = $this->httpClient->fetchUrl($url);
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
    
    /**
     *   Utility method that will add urls to a provided list 
     **/
    private function addUrlsToList($urls, $filterList, &$list)
    {
        foreach($urls as $newUrl){
            if (!in_array($newUrl, $filterList)){
                $list[] = $newUrl;
            }
        }
    }
}
