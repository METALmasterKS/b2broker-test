<?php

namespace App\Utils;

class Functions {
    /**
     * 
     * @param callable $callback
     * @param array $array
     * @return array
     */
    public static function array_map_keys($callback, array $array) {
        return array_combine( 
            array_map( $callback, array_keys($array) ), 
            $array
        );
    }
}
