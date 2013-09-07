<?php
namespace Geronimo\UrlFilter;

class Robots implements \Geronimo\UrlFilter\Rule {
    protected $rules = [];
    protected $userAgent;
    public function __construct($robotsFile, $userAgent = "geronimo"){
        $this->userAgent = $userAgent;
        $this->parse($robotsFile);
    }
    public function parse($str){
        $lines = explode("\n",$str);
        foreach ($lines as $line){

            $line = trim($line);
            // ignore comments
            
            if ($line && substr($line, 0,1) != "#"){
                $key = $value = "";
                list ($key, $value)  = explode(":", $line);
                $this->rules[] = ["type"=>strtolower(trim($key)), "value"=>trim($value)];
            }
        }
    }
    
    public function matches($url){
        
        $urlParts = parse_url($url);
        $rulesApply = false;
        $lastDirective = "";
        foreach($this->rules as $rule){
            switch($rule["type"]){
                case "allow":
                    if ($rulesApply && $this->pathPrefixOrUrlMatches($url, $rule["value"])) return true;                
                    break;
                case "disallow":
                    if ($rulesApply && $this->pathPrefixOrUrlMatches($url, $rule["value"])) {
                        return false;
                    }
                    break;
                case "user-agent":
                    if (strpos(strtolower($rule["value"]), strtolower($this->userAgent)) !== FALSE || $rule["value"] == "*"){
                        $rulesApply= true;
                    } else {
                        // Allow multiple concurrent user agent definition without resetting context. 
                        if ($lastDirective != "user-agent"){
                            $rulesApply = false;
                        }
                    }
                    break;
                case "sitemap":
                    //@not yet supported
                    break;
            }
            $lastDirective = $rule["type"];
        }

        return true;
    }
    
    protected function pathPrefixOrUrlMatches($url, $path){
        if ($path && strpos($path, $url) === 0 ||
            strpos(parse_url($url, PHP_URL_PATH), $path) === 0){
            return true;
        }
    }
}
