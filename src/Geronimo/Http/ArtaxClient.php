<?php

namespace Geronimo\Http;

class ArtaxClient implements Client {
    public function __construct(\Artax\Client $client){
        $this->client = $client;
    }
    public function fetchUrl($url){
        try{
            $response = $this->client->request($url);
            $ret = $this->createResponseArray($response);
            $ret["request_uri"] = $url;
            return $ret;
        } catch (Artex\ClientException $e){
            throw new Geronimo\Http\ClientException($e->getMessage(), $e->getCode(), $e);
        
        }
    }
    
    public function fetchUrls(array $urls){
        $requests = array_combine($urls, $urls);
        $responses = array();
        $this->client->requestMulti($requests,
                                    function($key, \Artax\Response $resp) use (&$responses) {
                                        static $c = 0;
                                        $c++;
                                        $responses[$key] = $resp;
                                        echo '[',$c,'] ',$key,"\n";
                                    },
                                    function($key, \Exception $resp){
                                        echo '[ERROR] : ',$key, "\n";
                                        //throw($resp);
                                        //die();
                                    });
        
        $ret = array();
        foreach($responses as $k=>$v){
            
            $ret[$k] = $this->createResponseArray($v);
            $ret[$k]["request_uri"] = $k;
        }

        return $ret;
    }
    
    private function createResponseArray(\Artax\Response $response){
            $ret = array();
            $ret["status_code"] = $response->getStatus();
            $ret["status_text"] = $response->getReason();
            $ret["headers"] = $response->getAllHeaders();
            $ret["body"] = $response->getBody();
            return $ret;
    }
}
