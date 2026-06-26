<?php
namespace SAF\Helpers;
defined( 'ABSPATH' ) || exit;

class DateFormatter {
    public static function formatRelative( string $date_string ): string {
        $timestamp = strtotime( $date_string );
        if ( ! $timestamp ) return $date_string;
        $diff = time() - $timestamp;
        if ( $diff < 0 ) return date_i18n( 'j F Y', $timestamp );
        $units = [
            YEAR_IN_SECONDS   => [ 'sing' => 'anno', 'plur' => 'anni' ],
            MONTH_IN_SECONDS  => [ 'sing' => 'mese', 'plur' => 'mesi' ],
            WEEK_IN_SECONDS   => [ 'sing' => 'settimana', 'plur' => 'settimane' ],
            DAY_IN_SECONDS    => [ 'sing' => 'giorno', 'plur' => 'giorni' ],
            HOUR_IN_SECONDS   => [ 'sing' => 'ora', 'plur' => 'ore' ],
            MINUTE_IN_SECONDS => [ 'sing' => 'minuto', 'plur' => 'minuti' ],
        ];
        foreach ( $units as $secs => $unit ) {
            $count = (int) floor( $diff / $secs );
            if ( $count >= 1 ) {
                $label = $count > 1 ? $unit['plur'] : $unit['sing'];
                return sprintf( '%d %s fa', $count, $label );
            }
        }
        return 'poco fa';
    }

    public static function formatItalian( string $date_string, bool $show_time = false ): string {
        $timestamp = strtotime( $date_string );
        if ( ! $timestamp ) return $date_string;
        $format = $show_time ? 'j F Y \a\ll\e H:i' : 'j F Y';
        return date_i18n( $format, $timestamp );
    }
}
