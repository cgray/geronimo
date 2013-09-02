<?php

namespace Geronimo\Http;

class FileGetContentsClient implements ClientInterface
{
    protected $finfo;
    
    public function __construct(){
        $this->finfo = finfo_open();
    }
    /**
     *  Get a url and process it
     *
     *  @param string $url The Url to fetch
     *  @return array Results of the callback parsing on the response
     **/
    public function fetchUrl($url)
    {
        $response = array();
        $start = microtime(true);
        $response["request_uri"] = $url;
        $response["body"] = file_get_contents($url);
        // emulate content size header
        $response["headers"]["content-type"] = finfo_buffer($this->finfo, $response["body"], FILEINFO_MIME_TYPE);
        $response["headers"]["content-length"] = strlen($response["body"]);
        $end = $response["elapsed_time"] = microtime(true);
        return $response;
    }
    
    /**
     *  Get and process an array of urls
     *
     *  @param array $urls Array of Urls
     *  @return array Array of Results keyed off of $url
     **/
    public function fetchUrls(array $urls)
    {
        $response = array();
        foreach($urls as $url){
            $response[$url] = $this->fetchUrl($url);
        }
        return $response;
    }
}
