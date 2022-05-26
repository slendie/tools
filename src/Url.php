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

    public static function isInternalLink( $domain_url, $page_url ) 
    {
        if ( Str::startsWith( 'http', $page_url ) ) {
            return Str::startsWith( $domain_url, $page_url );
        }
        if ( Str::startsWith( '/', $page_url ) && !startsWith( '//', $page_url ) ) {
            if ( Str::endsWith( '/', $domain_url ) ) {
                $domain = substr( $domain_url, -1 );
            } else {
                $domain = $domain_url;
            }
            $url = $domain_url . $page_url;
            return true;
        }
        if ( Str::startsWith( '//', $page_url ) ) {
            $pageUrl = Url( $domain_url );
            $protocol = $pageUrl->getProtocol();

            $url = $protocol . ':' . $page_url;

            return Str::startsWith( $domain_url, $url );
        }
        return false;
    }

    public static function getInternalLink( $domain_url, $page_url ) 
    {
        if ( Str::startsWith( 'http', $page_url ) ) {
            if ( Str::startsWith( $domain_url, $page_url ) ) {
                return $page_url;
            }
            return false;
        }
        if ( Str::startsWith( '/', $page_url ) && !startsWith( '//', $page_url ) ) {
            if ( Str::endsWith( '/', $domain_url ) ) {
                $domain = substr( $domain_url, -1 );
            } else {
                $domain = $domain_url;
            }
            $url = $domain_url . $page_url;
            return $url;
        }
        if ( Str::startsWith( '//', $page_url ) ) {
            $pageUrl = Url( $domain_url );
            $protocol = $pageUrl->getProtocol();

            $url = $protocol . ':' . $page_url;

            if ( Str::startsWith( $domain_url, $url ) ) {
                return $url;
            }
            return false;
        }
        return false;
    }

}