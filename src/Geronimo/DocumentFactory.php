<?php

namespace Geronimo;

class DocumentFactory
{
    protected $processors = [];
    protected $finfo;
    
    public function __construct()
    {
        $this->finfo = finfo_open();
    }
    
    public function createDocumentFromResponseArray(array $response){
        $contentType = $this->negotiateContent($response);
        if (array_key_exists($contentType, $this->processors)){
            $p = $this->processors[$contentType];
            $response = call_user_func([$p, "process"], $response);
        }
        $document = new Document;
        $document->setUrl($response["request_uri"]);
        $document->setBody($response["body"]);

        if (isset($response["headers"])){
            foreach($response["headers"] as $name => $value){
                $document->addHeader($name, $value);
            }
        }

        if (isset($response["links"])){
            foreach($response["links"] as $type=>$v){
                $document->addLinks($type, $v);
            }
        }
        if (isset($response["anchors"])){
            foreach($response["anchors"] as $k=>$v){
                $document->addAnchor($v);
            }
        }
        if (isset($response["images"])){
            foreach($response["images"] as $image){
                $document->addImage($image);
            }
        }
        if(isset($response["scripts"])){
            foreach($response["scripts"] as $v){
                $document->addScript($v);
            }
        }
        return $document;
    }
    
    public function addTypeHandler($type, Processor\ProcessorInterface $processor){
        $this->processors[$type] = $processor;
    }
    
    protected function negotiateContent($request){   
        return isset($request["header"]["Content-Type"])?$request["header"]["Content-Type"]:finfo_buffer($this->finfo, $request["body"], FILEINFO_MIME_TYPE);
    }
}
