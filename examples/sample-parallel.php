<?php


// install the the PSR-0 autoloader
namespace Geronimo;
require "../autoload.php";

// this is the url that will be used as the starting point for the crawl.
$url = "http://www.example.com/";

// create an httpClient... this is what will be used to actually fetch the resource.
$httpClient = new Http\FileGetContentsClient();

// Create the Url filter and add a filter rule that keeps crawling on the same domain (and allows subdomains).
$filter = new UrlFilter();
$sameDomainRule = new UrlFilter\SameDomainRule($url);
$sameDomainRule->allowSubdomains(true);

$filter->addFilterRule(new UrlFilter\SameDomainRule($url, true));

// This creates the component that will take a response array from the
// FileGetContentsClient and process it and create a Document object
$documentFactory = new DocumentFactory();

// Create the component that specializes in reading an html response array and
// parse out links and meta data. This takes a url strategy
$htmlProcessor = new Processor\HtmlProcessor();

// This registers the text/html mime type to the htmlProcessor, in the future I
// am imagining a text/css and application/pdf processor as well. 
$documentFactory->addTypeHandler('text/html', $htmlProcessor);


// An implementation of the Crawler Interface that will crawl by doing a request
// and process in a loop until all links have been exhausted. Part of the loop includes getting
// links from the document after it has been processed and adding them to the todo list if they
// haven't already been crawled. 
$crawler = new Crawler\ParallelCrawler($httpClient, $documentFactory, $filter);


$crawlResults = $crawler->crawl($url);

// After crawling run one or more reports 
$report = new Report\XmlSiteMap();
$report->bindData($crawlResults);
echo $report->generateReport();






