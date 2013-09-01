<?php
namespace Geronimo\Processor;

use Geronimo\UrlResolver;

class HtmlProcessor  implements ProcessorInterface  {
    use UrlResolver;
    
    public function process(array $response)
    {
        $start = microtime(true);
        $response["hrefs"] = array();
        $response["scripts"] = array();
        $response["links"] = array();
        $response["meta"] = array();

        $doc = new \DomDocument();
        libxml_use_internal_errors(true);
        $doc->encoding='utf-8';
        $doc->strictErrorChecking = false;
        $doc->loadHTML($response["body"]);
        libxml_clear_errors();
        
        $base = $doc->getElementsByTagName("base");

        if ($base->length){
            if ($base->item(0)->hasAttribute("href"))
            $response["base_url"] = $base->item(0)->getAttribute("href");
        } else {
            $response["base_url"] = $response["request_uri"];
        }
        // get the links
        $results = $doc->getElementsByTagName("a");
        foreach($results as $result){
            if ($result->hasAttribute("href") && substr($result->getAttribute("href"),0 ,1)!= "#"){
                $response["anchors"][] = $this->resolvePath($result->getAttribute("href"), $response["base_url"]);
            }
        }
        // get the script tags
        $results = $doc->getElementsByTagName("script");
        foreach($results as $result){
            if ($result->hasAttribute("src")){
                $response["scripts"][] = $this->resolvePath($result->getAttribute("src"), $response["base_url"]);
            }
        }
        
        $results = $doc->getElementsByTagName("link");
        foreach($results as $result){
            if ($result->hasAttribute("rel")){
                $rel = $result->getAttribute("rel");
                if (!isset($response["links"][$rel])){
                    $response["links"][$rel] = array();
                }
                $response["links"][$rel][] = $this->resolvePath($result->getAttribute("href"), $response["base_url"]);
            }
        }

        $results = $doc->getElementsByTagName("meta");
        foreach($results as $result){
            if($result->hasAttribute("name")){
                $response["meta"][$result->getAttribute("name")] = $result->getAttribute("content");
            }
        }
        $results = $doc->getElementsByTagName("img");
        foreach ($results as $result){
            if ($result->hasAttribute("src")){
                $response["images"][] = $this->resolvePath($result->getAttribute("src"), $response["base_url"]);
            }
        }
        
        return $response;
    }
}

?>