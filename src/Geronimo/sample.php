<?php


// install the the PSR-0 autoloader 
require "../../autoload.php";

// this is the url tha twill be used to start the crawl
$url = "http://www.example.com/";

// create an httpClient... this is what will be used to actually fetch the resource.
$httpClient = new \Geronimo\Http\FileGetContentsClient();

// create an instance of a url strategy, this is is a component that converts
// href like found in a <a> tag into a fully qualified url.  ie. /path/to/file.html
// gets turned into http://www.example.com/path/to/file.html.  In the furture this
// component should probably go away and the functionaility should live on the
// HtmlProcessor or on a trait that the HtmlProcessor has. 
$urlStrategy = new \Geronimo\UrlStrategy();

// This creates the component that will take a response array from the
// FileGetContentsClient and process it and create a Document object
$documentFactory = new \Geronimo\DocumentFactory();

// Create the component that specializes in reading an html response array and
// parse out links and meta data. This takes a url strategy
$htmlProcessor = new \Geronimo\Processor\HtmlProcessor($urlStrategy);

// This registers the text/html mime type to the htmlProcessor, in the future I
// am imagining a text/css and application/pdf processor as well. 
$documentFactory->addTypeHandler('text/html', $htmlProcessor);


// A sample Url Filter function that will work to keep the crawlings down to a single domain.  
$filter = function($test) use ($url){
    if (!is_string($test)) die(print_r($test, true));
    $ret = strpos($test, parse_url($url, PHP_URL_HOST));
    return $ret;
};

// An implementation of the \Geronimo\Crawler Interface that will crawl by doing a request
// and process in a loop until all links have been exhausted. Part of the loop includes getting
// links from the document after it has been processed and adding them to the todo list if they
// haven't already been crawled. 
$crawler = new \Geronimo\Crawler\SequencialCrawler($httpClient, $documentFactory, $filter);


$results = $crawler->crawl($url);
//print_r($results);
/*
echo array_walk($results, function($a){

    echo $a->getUrl()."************************************************\n";
    
    echo "\tANCHORS\n\t===================\n\t".implode("\n\t", $a->getAnchors())."\n";
});*/
/*$report = new Report\XmlSiteMap();
$sitemap = $report->run($results);
*/

