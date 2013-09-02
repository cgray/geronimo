<?php

namespace Geronimo;

class UrlFilter {
    public function addFilterRule(UrlFilter\Rule $rule)
    {
        $this->filterRules[] = $rule;
    }
    
    public function isAllowed($url)
    {
        foreach($this->filterRules as $rule){
            if (!$rule->matches($url)) return false;
        }
        return true;
    }
}
