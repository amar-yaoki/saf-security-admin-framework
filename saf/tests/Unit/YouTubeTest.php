<?php

use PHPUnit\Framework\TestCase;
use SAF\Helpers\YouTube;

class YouTubeTest extends TestCase {
    public function testStandardUrl(): void {
        $url = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
        $expected = 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ';
        $this->assertEquals( $expected, YouTube::getEmbedUrl( $url ) );
    }

    public function testShortUrl(): void {
        $url = 'https://youtu.be/dQw4w9WgXcQ';
        $expected = 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ';
        $this->assertEquals( $expected, YouTube::getEmbedUrl( $url ) );
    }

    public function testEmbedUrl(): void {
        $url = 'https://www.youtube.com/embed/dQw4w9WgXcQ';
        $expected = 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ';
        $this->assertEquals( $expected, YouTube::getEmbedUrl( $url ) );
    }

    public function testInvalidUrl(): void {
        $this->assertEquals( '', YouTube::getEmbedUrl( 'https://example.com' ) );
    }

    public function testRenderEmbed(): void {
        $html = YouTube::renderEmbed( 'https://youtu.be/dQw4w9WgXcQ', 'Test Title' );
        $this->assertStringContainsString( 'youtube-nocookie.com', $html );
        $this->assertStringContainsString( 'Test Title', $html );
        $this->assertStringContainsString( 'saf-video-wrapper', $html );
    }
}
