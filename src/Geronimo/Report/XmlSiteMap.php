<?php
namespace Geronimo\Report;

class XmlSiteMap {
    protected $data;
    public function bindData(&$data){
        $this->data = $data;
    }
    public function generateReport(){
        $data = $this->data;
        //Ugly ugly over simplified implementation for proof of concept;
        $dom = new \DomDocument("1.0");
        $urlset = $dom->createElementNS("http://www.sitemaps.org/schemas/sitemap/0.9" ,"urlset");
        foreach($data as $node){
            if ($node->getHeader("Content-Type", "text/html") == "text/html"){
                $url = $dom->createElement("url");
                $url->appendChild($dom->createElement("loc", $node->getCanonicalUrl()));
                $url->appendChild($dom->createElement("lastmod", date("Y-m-d")));
                $url->appendChild($dom->createElement("changefreq", "weekly"));
                $url->appendChild($dom->createElement("priority","0.5"));
                $urlset->appendChild($url);
            }
        }
        $dom->appendChild($urlset);
        return $dom->saveXml();
    }
}
