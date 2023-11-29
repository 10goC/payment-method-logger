<?php
namespace PaymentMethodLogger\Util;

class Colorizer
{
    /**
     * Add colors to JSON formatted string
     */
    public static function pretty_print( $json )
    {
        $lines = explode( PHP_EOL, $json );
        $out = [];
        foreach ( $lines as $line ) {
            $parts = explode( ':', $line );
            if ( count( $parts ) > 1 ) {
                $key = $parts[0];
                $value = trim( substr( $line, strlen( $key ) + 1 ) );
                $key = '<span class="key">' . $key . '</span>';
                if ( is_numeric( trim( $value, ',' ) ) ) {
                    $value = '<span class="number">' . $value . '</span>';
                } else if ( preg_match( '/(true|false)/i', $value ) ) {
                    $value = '<span class="boolean">' . $value . '</span>';
                } else if ( in_array( trim( $value, ',' ), [ '[]', '{}', '""' ] ) ) {
                    $value = '<span class="empty">' . $value . '</span>';
                } else {
                    $value = '<span class="string">' . $value . '</span>';
                }
                $out[] = $key . ': ' . $value;
            } else {
                $out[] = $line;
            }
        }
        return implode( PHP_EOL, $out );
    }
}