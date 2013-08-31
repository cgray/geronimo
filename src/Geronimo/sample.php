<?php



require "../../autoload.php";

$url = "http://www.example.com/";
$httpClient = new \Geronimo\Http\FileGetContentsClient();
$urlStrategy = new \Geronimo\UrlStrategy();
$documentFactory = new \Geronimo\DocumentFactory();
$documentFactory->addTypeHandler('text/html', new \Geronimo\Processor\HtmlProcessor($urlStrategy));

$filter = function($test) use ($url){
    if (!is_string($test)) die(print_r($test, true));
    $ret = strpos($test, parse_url($url, PHP_URL_HOST));
    return $ret;
};

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

