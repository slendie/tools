<?php
namespace Slendie\Tools;

class Str
{
    private function __construct() {}

    public static function startsWith( $prefix, $term ) 
    {
        if ( empty( $prefix ) || empty( $term ) ) return false;
        if ( strlen( $prefix ) > strlen( $term ) ) return false;

        if ( substr( $term, 0, strlen( $prefix ) ) == $prefix ) {
            return true;
        }
        return false;
    }

    public static function endsWith( $sufix, $term )
    {
        if ( empty( $sufix ) || empty( $term ) ) return false;
        if ( strlen( $sufix ) > strlen( $term ) ) return false;

        $start = strlen( $sufix ) * (-1);

        if ( substr( $term, $start ) == $sufix ) {
            return true;
        }
        return false;
    }

    private static function processWords( $term, $words )
    {
        foreach( $words as $word => $instructions ) {
            if ( strtolower( $term ) == $word || $word == '*' ) {
                foreach( $instructions['operations'] as $step ) {
                    $steps = explode('|', $step);
                    switch ( $steps[0] ) {
                        case '-':
                            $term = substr( $term, 0, ( (int) $steps[1] ) * -1 );
                            break;

                        case '+':
                            $term .= $steps[1];
                            break;
                    }
                }
                return $term;
            }
        }
        return false;
    }

    public static function plural( $term )
    {
        $test = strtolower( $term );

        $word_exceptions = [
            'child'     => 
                [   
                    'plural'        => 'children',
                    'operations'    => [
                        '+|ren',
                    ],
                ],
            'person'    => 
                [
                    'plural'        => 'people',
                    'operations'    => [
                        '-|4',
                        '+|ople',
                    ],
                ],
        ];

        // Ending with 'R' or 'Z'
        $word_group_1_exceptions = [
            'car'    => 
                [
                    'plural'        => 'cars',
                    'operations'    => [
                        '+|s',
                    ],
                ],
            'user'    => 
                [
                    'plural'        => 'users',
                    'operations'    => [
                        '+|s',
                    ],
                ],
            '*'       =>
                [
                    'plural'        => '*',
                    'operations'    => [
                        '+|es',
                    ],
                ],
    ];

        // Ending with 'L'
        $word_group_2_exceptions = [
            'mal'    => 
                [
                    'plural'        => 'males',
                    'operations'    => [
                        '+|es',
                    ],
                ],
            '*'       =>
                [
                    'plural'        => '*',
                    'operations'    => [
                        '-|1',
                        '+|is',
                    ],
                ],
        ];

        // Check exceptions
        $finding = self::processWords( $term, $word_exceptions );
        if ( $finding !== false ) return $finding;

        // Ending with special characters - group 1
        if ( self::endsWith( 'r', $test ) || self::endsWith( 'z', $test ) ) {
            $finding = self::processWords( $term, $word_group_1_exceptions );
            if ( $finding !== false ) return $finding;
        }

        // Ending with special characters - group 2
        if ( self::endsWith( 'l', $test ) ) {
            $finding = self::processWords( $term, $word_group_2_exceptions );
            if ( $finding !== false ) return $finding;
        }

        return $term . "s";
    }

    public static function singular( $term )
    {
        $test = strtolower( $term );

        $word_exceptions = [
            'children'     => 
                [   
                    'singular'        => 'child',
                    'operations'    => [
                        '-|3',
                    ],
                ],
            'people'    => 
                [
                    'singular'        => 'person',
                    'operations'    => [
                        '-|4',
                        '+|rson',
                    ],
                ],
        ];

        // Ending with 'IES'
        $word_group_1_exceptions = [
            '*'       =>
                [
                    'singular'        => '*',
                    'operations'    => [
                        '-|3',
                        '+|y',
                    ],
                ],
        ];

        // Ending with 'ES'
        $word_group_2_exceptions = [
            '*'       =>
                [
                    'singular'        => '*',
                    'operations'    => [
                        '-|2',
                    ],
                ],
        ];



        // Check exceptions
        $finding = self::processWords( $term, $word_exceptions );
        if ( $finding !== false ) return $finding;

        // Ending with special characters - group 1
        if ( self::endsWith( 'ies', $test ) ) {
            $finding = self::processWords( $term, $word_group_1_exceptions );
            if ( $finding !== false ) return $finding;
        }

        // Ending with special characters - group 2
        if ( self::endsWith( 'es', $test ) && !self::endsWith( 'ies', $test ) ) {
            $finding = self::processWords( $term, $word_group_2_exceptions );
            if ( $finding !== false ) return $finding;
        }

        return substr( $term, 0, -1 );
    }

}