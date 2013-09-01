<?php


// install the the PSR-0 autoloader 
require "../../autoload.php";

// this is the url that will be used as the starting point for the crawl.
$url = "http://www.example.com/";

// create an httpClient... this is what will be used to actually fetch the resource.
$httpClient = new \Geronimo\Http\FileGetContentsClient();

// This creates the component that will take a response array from the
// FileGetContentsClient and process it and create a Document object
$documentFactory = new \Geronimo\DocumentFactory();

// Create the component that specializes in reading an html response array and
// parse out links and meta data. This takes a url strategy
$htmlProcessor = new \Geronimo\Processor\HtmlProcessor();

// This registers the text/html mime type to the htmlProcessor, in the future I
// am imagining a text/css and application/pdf processor as well. 
$documentFactory->addTypeHandler('text/html', $htmlProcessor);


// A sample Url Filter function that will work to keep the crawlings down to a single domain.  
$filter = function($test) use ($url){
    $ret = strpos($test, parse_url($url, PHP_URL_HOST));
    return $ret;
};

// An implementation of the \Geronimo\Crawler Interface that will crawl by doing a request
// and process in a loop until all links have been exhausted. Part of the loop includes getting
// links from the document after it has been processed and adding them to the todo list if they
// haven't already been crawled. 
$crawler = new \Geronimo\Crawler\SequencialCrawler($httpClient, $documentFactory, $filter);


$results = $crawler->crawl($url);

// After crawling run one or more reports (not yet implemented)

/*$report = new Report\XmlSiteMap();
$sitemap = $report->run($results);
echo $sitemap;
*/

print_r($results);
//print_r($results);



