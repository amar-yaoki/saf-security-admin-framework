<?php
defined( 'ABSPATH' ) || exit;
namespace SAF;

class Autoloader {
    public static function register(): void {
        spl_autoload_register( function ( string $class ): void {
            $prefix = 'SAF\\';
            if ( strncmp( $class, $prefix, strlen( $prefix ) ) !== 0 ) return;
            $relative = substr( $class, strlen( $prefix ) );
            $file = __DIR__ . '/' . str_replace( '\\', '/', $relative ) . '.php';
            if ( file_exists( $file ) ) require $file;
        } );
    }
}
