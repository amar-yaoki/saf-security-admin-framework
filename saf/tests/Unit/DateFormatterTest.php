<?php
/**
 * Example unit test for SAF\Helpers\DateFormatter.
 */

use PHPUnit\Framework\TestCase;
use SAF\Helpers\DateFormatter;

class DateFormatterTest extends TestCase {
    public function testFormatRelativeYear(): void {
        $result = DateFormatter::formatRelative( date( 'Y-m-d', strtotime( '-2 years' ) ) );
        $this->assertStringContainsString( '2 anni fa', $result );
    }

    public function testFormatRelativeMonth(): void {
        $result = DateFormatter::formatRelative( date( 'Y-m-d', strtotime( '-3 months' ) ) );
        $this->assertStringContainsString( '3 mesi fa', $result );
    }

    public function testFormatItalian(): void {
        $result = DateFormatter::formatItalian( '2024-01-15' );
        $this->assertStringContainsString( 'Gennaio', $result );
        $this->assertStringContainsString( '15', $result );
    }

    public function testInvalidDate(): void {
        $result = DateFormatter::formatItalian( '' );
        $this->assertEquals( '', $result );
    }
}
