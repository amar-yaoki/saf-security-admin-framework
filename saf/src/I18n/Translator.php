<?php
defined( 'ABSPATH' ) || exit;
namespace SAF\I18n;

class Translator {
    private static ?array $strings = null;
    private static bool $loaded = false;

    public function init(): void {
        add_action( 'init', [ $this, 'loadTextdomain' ] );
    }

    public function loadTextdomain(): void {
        load_plugin_textdomain( 'saf', false, dirname( plugin_basename( SAF_DIR ) ) . '/languages/' );
    }

    public static function get( string $key ): string {
        if ( ! self::$loaded ) {
            $locale = get_locale();
            $it_file = SAF_DIR . 'languages/it_IT.php';
            self::$strings = file_exists( $it_file ) ? ( require $it_file ) : [];
            if ( strpos( $locale, 'it_' ) !== 0 ) {
                $lang_file = SAF_DIR . 'languages/' . $locale . '.php';
                if ( file_exists( $lang_file ) ) {
                    $lang_strings = require $lang_file;
                    if ( is_array( $lang_strings ) ) {
                        self::$strings = array_merge( self::$strings, $lang_strings );
                    }
                }
            }
            self::$loaded = true;
        }
        return self::$strings[ $key ] ?? $key;
    }
}
