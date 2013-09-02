<?php
namespace Geronimo\UrlFilter;
interface Rule
{
    public function matches($url);
}
