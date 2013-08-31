<?php

namespace Geronimo;

class Document {
    protected $canonicalUrl;
    protected $requestUrl;
    protected $body;
    protected $headers = [];
    protected $links = [];
    protected $anchors = [];
    protected $images = [];
    protected $scripts = [];
    
    
    public function setBody($body){
        $this->contentBody = $body;
    }
    
    public function getBody(){
        return $this->contentBody;
    }
    public function addHeader($header, $value){
          $this->headers[$header] = $value;
    }
    public function getHeader($header, $default = null){
        return (array_key_exists(strtolower($header), $this->headers))
                ?$this->headers[strtolower($header)]
                :$default;
    }
    
    public function getContentLength(){
        return strlen($this->body);
    }
    
    public function setCanonicalUrl($url){
        $this->canonicalUrl = $url;
    }
    public function getCanonicalUrl(){
        return $this->canonicalUrl?$this->canonicalUrl:$this->requestUrl;
    }
    
    public function getAnchors(){
        return $this->anchors;
    }
    
    public function getImages(){
        return $this->images;
    }
    
    public function getScripts(){
        return $this->scripts;
    }
    
    public function getStyleSheets(){
        return isset($this->links["stylesheet"])?$this->links["stylesheet"]:[];
    }
    
    public function clearBody(){
        $this->contentBody = null;
    }
    public function addLinks($type, $links){
        if(!isset($this->links[$type])){
            $this->links[$type] = $links;
        } else {
            $this->links[$type] += $value;
        }
    }    
    public function addLink($type, $value){
        if(!isset($this->links[$type])) $this->links[$type] = [];
        $this->links[$type][] = $value;
    }
    public function addScript($src){
        $this->scripts[] = $src;
    }
    public function addImage($url){
        $this->images[] = $url;
    }
    public function addAnchor($url){
        $this->anchors[] = $url;
    }
    
    public function getUrl(){
        return $this->requestUrl;
    }
    public function setUrl($url) {
        $this->requestUrl = $url;
    }
    

}
