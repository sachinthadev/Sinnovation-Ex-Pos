<?php

namespace Tests\Unit;

use App\Filament\Resources\OrderResource;
use PHPUnit\Framework\TestCase;

class OrderResourceTest extends TestCase
{
    public function test_order_resource_registers_view_page(): void
    {
        $pages = OrderResource::getPages();

        $this->assertArrayHasKey('view', $pages);
        $this->assertSame('view', array_key_first(array_filter($pages, fn ($page) => $page->getPage() === \App\Filament\Resources\OrderResource\Pages\ViewOrder::class, ARRAY_FILTER_USE_BOTH)));
    }
}
