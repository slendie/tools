<?php
namespace Slendie\Tools;

class Collection
{
    protected $data = array();

    public function __set( $key, $value )
    {
        $this->data[ $key ] = $value;
    }

    public function __get( $key )
    {
        return $this->data[ $key ];
    }

    public function fromArray( array $array )
    {
        $this->data = $array;
    }

    public function toArray()
    {
        return $this->data;
    }

    public static function arrayPluck( $array, $key ) 
    {
        return array_map( function( $v ) use ( $key ) {
            return is_object( $v ) ? $v->$key : $v[ $key ];
        });
    }

    public function pluck( $key )
    {
        return self::arrayPluck( $this->data, $key );
    }
    
}