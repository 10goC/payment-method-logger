<?php
namespace PaymentMethodLogger\Util;

class FileWriter
{
    /**
     * Write data to a file
     */
    public static function write( $filename, $data )
    {
        $dir = dirname( $filename );
        if ( !is_dir($dir) ) {
            $created = mkdir($dir, 0777, true);
            if ( !$created ) {
                // Fail gracefully.
                // We can't create the directory to write the file,
                // but we don't want the end user to see an error.
                return;
            }
        }
        file_put_contents( $filename, $data );
    }
}
