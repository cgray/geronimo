<?php

namespace Geronimo\Crawler;
/**
 * Implements a Syncronous Strategry for Crawling a website.
 **/


class ParallelCrawler extends \Geronimo\Crawler\AbstractCrawler implements \Geronimo\Crawler
{
    public $maxConnections = 10;
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
        while (count($todoList)){
            
            $urls = $todoList;
            $todoList = [];
            $urls = array_filter($urls, array($this->urlFilter, "isAllowed"));
            $urls = array_map(function($url){
                $pos = strpos($url, "#");
                return $pos !== FALSE?substr($url, 0, $pos):$url;
            }, $urls);
            $urls = array_diff($urls, $crawledUrls);
            if (count($urls)){
                $urls = array_chunk($urls, (int)$this->maxConnections);
                foreach($urls as $urlchunk){
                    $responses = $this->httpClient->fetchUrls($urlchunk);
                    foreach($responses as $response){
                        $document = $this->documentFactory->createDocumentFromResponseArray($response);
                        if(!in_array($document->getCanonicalUrl(), $crawledUrls)){
                            $crawledUrls[] = $document->getCanonicalUrl();
                            if ($document->getCanonicalUrl() != $document->getUrl()){
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
                    usleep($this->cooldown);
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
