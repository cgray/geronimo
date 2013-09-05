<?php

namespace Geronimo;

trait UrlResolver {
    protected function resolvePath($href, $context){
        // Determine what kind of href we got
        $hrefParts = parse_url($href);
        $contextParts = parse_url($context);
        if (!isset($contextParts["path"])){
            $contextParts["path"] = "/";
        }
        if (isset($hrefParts["scheme"])) {
            // we have a fully qualified url : http://www.example.com/path/to/file.php
            return $href;
        } else if (isset($hrefParts["host"])) {
            // we have a protocol relative url : //www.example.com/path/to/file.php
            return $contextParts["scheme"]."://".$href;
        } else {

            if (substr($contextParts["path"],-1) == "/"){
                $pathComponents = explode("/", trim($contextParts["path"],"/"));
            } else {

                $pathComponents = explode("/", trim(dirname($contextParts["path"]), "/"));
            }
            $pathComponents = array_filter($pathComponents);
            if (isset($hrefParts["path"])){
                $relComponents  = explode("/", trim($hrefParts["path"], "/"));
             } else {
                $relComponents = [];
             }
            foreach ($relComponents as $dir){
                if ($dir !== null){
                    if ($dir == ".."){
                        array_pop($pathComponents);
                    } else {
                        array_push($pathComponents, $dir);
                    }
                }
            }    
            
            // rebuild the url from all of the parts
            $url = $contextParts["scheme"]."://".$contextParts["host"];
            if (isset($contextParts["port"])){
                $url.= ":".$contextParts["port"];
            }
            $url .= "/".implode("/", $pathComponents);
            if (isset($hrefParts["query"])){
                $url.= "?".$hrefParts["query"];
            }
            if (isset($hrefParts["fragment"])){
                $url.= "#".$hrefParts["fragment"];
            }
            return $url;
        }
    }
}