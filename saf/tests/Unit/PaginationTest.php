<?php

use PHPUnit\Framework\TestCase;
use SAF\Helpers\Pagination;

class PaginationTest extends TestCase {
    public function testEmptyQuery(): void {
        // Mock WP_Query with no pages
        $query = $this->createMock( WP_Query::class );
        $query->max_num_pages = 0;
        $result = Pagination::render( $query );
        $this->assertEquals( '', $result );
    }

    public function testSinglePage(): void {
        $query = $this->createMock( WP_Query::class );
        $query->max_num_pages = 1;
        // With 1 page, paginate_links returns empty array → empty output
        $result = Pagination::render( $query );
        $this->assertEquals( '', $result );
    }
}
