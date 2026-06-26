<?php
/**
 * Integration test stub for SAF v2 modules.
 * Requires WordPress test suite.
 */

use PHPUnit\Framework\TestCase;

class SecurityModuleTest extends TestCase {
    public function testModuleInit(): void {
        $module = new \SAF\Modules\Security();
        $this->assertTrue( method_exists( $module, 'init' ) );
        $this->assertTrue( method_exists( $module, 'removeGeneratorTag' ) );
        $this->assertTrue( method_exists( $module, 'disableXpingback' ) );
    }
}
