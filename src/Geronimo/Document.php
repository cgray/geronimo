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
    
    
    /**
     * @param string $body the document body.
     **/
    public function setBody($body)
    {
        $this->contentBody = $body;
    }

    /**
     * @return string body
     **/
    public function getBody()
    {
        return $this->contentBody;
    }
    
    /**
     * Sets a header
     *
     * @param string $header
     * @param string $value
     **/
    public function addHeader($header, $value)
    {
          $this->headers[$header] = $value;
    }
    
    /**
     * Get a header (or a default if one isn't defined)
     * @param string $header The name of the header. i.e. Content-Type
     * @param string $default What to return if the header is not defined
     **/
    
    public function getHeader($header, $default = null)
    {
        return (array_key_exists(strtolower($header), $this->headers))
                ?$this->headers[strtolower($header)]
                :$default;
    }

    /**
     * Get the length of the body
     * 
     * @return int Length of the document body in bytes.
     **/
    public function getContentLength()
    {
        return strlen($this->body);
    }
    
    /**
     * @param string $url The canonical url as defined by the cannonical meta header
     **/
    public function setCanonicalUrl($url)
    {
        $this->canonicalUrl = $url;
    }
    
    /**
     * Gets the Canonical Url from the document, or the request url if none was defined.
     **/
    public function getCanonicalUrl()
    {
        return $this->canonicalUrl?$this->canonicalUrl:$this->requestUrl;
    }

    /**
     *    Gets the anchors from a document
     *
     *    @return array Array of anchor href urls
     **/
    public function getAnchors()
    {
        return $this->anchors;
    }
    
    /**
     * Get the images from a document
     *
     * @return array Array of image urls
     **/
    public function getImages()
    {
        return $this->images;
    }
    
    /**
     * Get the scripts from the document
     * @return array Array of script urls
     **/
    public function getScripts()
    {
        return $this->scripts;
    }
    
    /**
     * Get style sheets from te docuement
     *
     * @return array Array of stylesheet urls
     **/
    public function getStyleSheets()
    {
        return isset($this->links["stylesheet"])?$this->links["stylesheet"]:[];
    }
    /**
     * Clears the body of the document
     **/
    public function clearBody()
    {
        $this->contentBody = null;
    }
    
    /**
     *  Add a collection of links to the document
     *  @param string $type The rel attribute for the link
     *  @param array $value An array of hrefs
     **/
    public function addLinks($type, $links)
    {
        if(!isset($this->links[$type])){
            $this->links[$type] = $links;
        } else {
            $this->links[$type] += $value;
        }
    }
    
    /**
     * Add the href for a link elment
     *
     * @param string $type The value of the rel attribute for the link
     * @param string $value The value of the href for the link
     **/
    public function addLink($type, $value)
    {
        if(!isset($this->links[$type])) $this->links[$type] = [];
        $this->links[$type][] = $value;
    }
    
    /**
     *  Add a script src to the docucument
     *
     *  @param string $url
     **/
    public function addScript($src)
    {
        $this->scripts[] = $src;
    }
    
    /**
     * Add an image src to the list of images
     *
     * @param string $url The image src attribute
     **/
    public function addImage($url)
    {
        $this->images[] = $url;
    }
    /**
     * Add an anchor to the list of anchors
     *
     * @param string $url
     **/
    public function addAnchor($url)
    {
        $this->anchors[] = $url;
    }
    /**
     * Get the requested url for this document
     * 
     * @return string The Request Url
     **/
    public function getUrl()
    {
        return $this->requestUrl;
    }
    
    /**
     * Set the requested url for this document.
     * 
     * @param string $url
     **/
    public function setUrl($url)
    {
        $this->requestUrl = $url;
    }
    

}
