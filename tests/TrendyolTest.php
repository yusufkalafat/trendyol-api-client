<?php

use PHPUnit\Framework\TestCase;

require 'src/Trendyol.php';

class TrendyolTest extends TestCase
{
    private $trendyol;

    protected function setUp(): void
    {
        $this->trendyol = new Trendyol('SUPPLIER_ID', 'API_KEY', 'API_SECRET');
    }

    public function testGetOrders()
    {
        $orders = $this->trendyol->getOrders('Delivered');
        $this->assertIsArray($orders);
    }

    public function testUpdateStock()
    {
        $response = $this->trendyol->updateStock(12345, 10);
        $this->assertTrue($response);
    }

    public function testUpdatePrice()
    {
        $response = $this->trendyol->updatePrice(12345, 199.99);
        $this->assertTrue($response);
    }
}