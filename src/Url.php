<?php
namespace Slendie\Tools;

class Url
{
    protected $url;

    public function setUrl( $url )
    {
        $this->url = $url;
    }

    public function getUrl( $url )
    {
        return $this->url;
    }

    public function normalize()
    {
        if ( substr( $this->url, -1, 1 ) == '/' && strlen( $this->url ) > 2 ) {
            $this->url = substr( $this->url, 0, strlen( $this->url ) - 1 );
        }
        return $this->url;
    }

    public function getProtocol()
    {
        $url = strtolower( $this->url );
        $pattern = '/((?:http|https)+)(?:\:\/\/)([^\/]*)/';

        preg_match( $pattern, $this->url, $matches );

        return $matches[1];
    }

    public function getDomain()
    {
        $url = strtolower( $this->url );
        $pattern = '/(?:http|https)?(?:\:\/\/)([^\/]*)/';

        preg_match( $pattern, $this->url, $matches );

        return $matches[1];
    }
}