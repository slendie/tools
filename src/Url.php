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

    public function getBase()
    {
        $url = strtolower( $this->url );
        $pattern = '/(?:http|https)?(?:\:\/\/)(.*)/';

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

    public static function getCompleteUrl( $target_url, $source_url )
    {
        // Sanitize URL
        $target_url = trim( $target_url );
        $source_url = trim( $source_url );
        
        $pattern = '/^([\w]+:(\/\/)?)/';
        if ( preg_match( $pattern, $target_url, $match ) ) {
            return $target_url;
        }
        if ( Str::endsWith( '/', $source_url ) ) {
            $source_url = substr( $source_url, 0, -1 );
        }

        $url = new Url();
        $url->setUrl( $source_url );
        $protocol = $url->getProtocol();
        $source_url = $url->getBase();

        if ( Str::startsWith( '/', $target_url ) && !startsWith( '//', $target_url ) ) {
            $source_parts = explode('/', $source_url);

            return $protocol . '://' . $source_parts[0] . $target_url;
        }

        if ( Str::startsWith( '//', $target_url ) ) {
            return $protocol . ':' . $target_url;
        }

        $source_parts = array_filter( explode('/', $source_url) );

        $last_segment = array_pop( $source_parts );
        $final_url = '';
        if ( count( $source_parts ) > 0 ) {
            foreach( $source_parts as $source_part ) {
                $final_url .= $source_part . '/';
            }
            $final_url .= $last_segment;
            $pattern = '/[\.#\?]+/';
            if ( !preg_match( $pattern, $last_segment ) ) {
                // $final_url .= $last_segment . '/';
                $final_url .= '/';
            }
        } else {
            $final_url = $last_segment;
            $pattern = '/[\.#\?]+/';
            if ( !preg_match( $pattern, $last_segment ) ) {
                $final_url .= '/';
            }
        }

        /* target starting with '#' or '?' may concatenate with the base url. */
        if ( Str::startsWith( '#', $target_url ) || Str::startsWith( '?', $target_url ) ) {
            if ( Str::endsWith( '/', $final_url ) ) {
                $final_url = substr( $final_url, 0, -1 );
            }
        } else {
            /* Prevent '//' between base and target url */
            if ( Str::endsWith( '/', $final_url ) && Str::startsWith( '/', $target_url ) ) {
                $final_url = substr( $final_url, 0, -1);
            } else {
                /* Prevent missing '/' between base and target url */
                if ( !Str::endsWith( '/', $final_url ) && !Str::startsWith( '/', $target_url ) ) {
                    $final_url .= '/';
                }
            }
        }

        return $protocol . '://' . $final_url . $target_url;
    }
}