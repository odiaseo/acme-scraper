<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\ScraperService;
use PHPUnit\Framework\TestCase;

class ScraperServiceTest extends TestCase
{
    private ScraperService $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->service = new ScraperService();
    }

    public function testThatProductInformationAreScraped(): array
    {
        $url = 'https://wltest.dns-systems.net/';
        $productList = $this->service->getProducts($url);

        $this->assertSame(6, count($productList));

        return $productList;
    }

    /**
     * @depends testThatProductInformationAreScraped
     */
    public function testThatTheProductListHasTheRequiredAttributes(array $products): void
    {
        $requiredKeys = [
            'title',
            'description',
            'price',
            'annualPrice',
            'discount',
        ];

        foreach ($products as $product) {
            foreach ($requiredKeys as $key) {
                $this->assertArrayHasKey($key, $product);
            }
        }
    }
}
