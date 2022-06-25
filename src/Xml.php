<?php
namespace Slendie\Tools;

use \SimpleXmlElement;

class Xml
{
    public static function arrayToXml( $array )
    {
        $xml = new SimpleXmlElement('<?xml version="1.0" encoding="utf-8"?><root></root>');

        self::addArrayToXml( $array, $xml );

        return $xml->asXML();
    }

    private static function addArrayToXml( $array, $xml )
    {
        if ( is_array( $array ) ) {
            foreach( $array as $key => $value ) {
                if ( is_int( $key ) ) {
                    if ( $key == 0 ) {
                        $node = $xml;
                    } else {
                        $parent = $xml->xpath('..')[0];
                        $node = $parent->addChild( $xml->getName() );
                    }
                } else {
                    $node = $xml->addChild( $key );
                }
                self::addArrayToXml( $value, $node );
            }
        } else {
            $xml[0] = $array;
        }
    }
}