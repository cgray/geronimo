<?php
namespace Geronimo\Http;

interface Client
{
    /**
     *  Get a url and process it
     *
     *  @param string $url The Url to fetch
     *  @param Callable $callback Callback used to process the result of the request.
     *  @return array Results of the callback parsing on the response
     **/
    public function fetchUrl($url);
    
    /**
     *  Get and process an array of urls
     *
     *  @param array $urls Array of Urls
     *  @param Callable $callback Callback used to process the result of each request.
     *  @return array Array of Results keyed off of $url
     **/
    public function fetchUrls(array $urls);
}


