<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DomCrawler\Crawler;

class ScraperService
{
    /**
     * Scrape and list products found in descending order of annual price.
     */
    public function getProducts(string $url): array
    {
        $htmlPage = $this->getPageContent($url);
        $crawler = new Crawler($htmlPage);

        $pricingQuery = '//section[@id="subscriptions"]//div//div[@class="pricing-table"]';
        $crawler = $crawler->filterXPath($pricingQuery);

        $subscriptionList = [];

        $crawler->each(function (Crawler $node) use (&$subscriptionList) {
            $subscriptionList = array_merge($subscriptionList, $this->processPricingTable($node));
        });

        if (empty($subscriptionList)) {
            return $subscriptionList;
        }
        usort($subscriptionList, function ($a, $b) {
            return $a['annualPrice'] < $b['annualPrice'];
        });

        return $subscriptionList;
    }

    /**
     * Scrapes the Website site and returns the HTML content of the page.
     */
    public function getPageContent(string $url): bool|string
    {
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36',
            CURLOPT_REFERER => 'https://www.google.com',
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, $options);

        return curl_exec($ch);
    }

    /**
     * Search for the package section on the page and process the rows.
     */
    public function processPricingTable(Crawler $node): array
    {
        $subscriptionList = [];
        $options = $node->filter('.package');

        if (!$options->count()) {
            return $subscriptionList;
        }

        $options->each(function (Crawler $node2, $j) use (&$subscriptionList) {
            $subscriptionList[$j] = $this->processPricingOption($node2);
        });

        return $subscriptionList;
    }

    /**
     * Search for the package sections and retrieve the options.
     */
    public function processPricingOption(Crawler $node): array
    {
        $subscriptionList = [];
        $headingH3 = $node->filter('.header > h3');

        if ($headingH3->count()) {
            $heading = $headingH3->text();
            $subscriptionList['title'] = $heading;
        }

        $details = $this->getOptionDetails($node);

        return array_merge($subscriptionList, $details);
    }

    /**
     * Get the details of the package options.
     */
    public function getOptionDetails(Crawler $node): array
    {
        $subscriptionList = [];
        $itemList = $node->filter('div ul > li');

        if (!$itemList->count()) {
            return $subscriptionList;
        }

        $itemList->each(function (Crawler $list) use (&$subscriptionList) {
            $element = $list->filter('div');
            $details = $this->processLineDetail($element);

            $subscriptionList = array_merge($subscriptionList, $details);
        });

        return $subscriptionList;
    }

    /**
     * Process each line details and itemise based on the class name.
     */
    public function processLineDetail(Crawler $list): array
    {
        $subscriptionList = [];

        $element = $list->filter('div');
        $class = $element->attr('class');
        $content = $list->filter('div')->text();

        if ('package-price' === $class) {
            $subscriptionList = array_merge($subscriptionList, $this->processPrice($element, $class, $content));
        } else {
            $subscriptionList[$this->getKey($class)] = $content;
        }

        return $subscriptionList;
    }

    /**
     * Check for the price and compute the annual prices if it is a monthly package.
     */
    public function processPrice(Crawler $element, string $class, string $content): array
    {
        $subscriptionList = [];

        $price = $element->filter('.price-big')->text();
        $numericPrice = preg_replace('/[^0-9.]+/', '', $price);
        $subscriptionList[$this->getKey($class)] = $numericPrice;
        $subscriptionList['priceInfo'] = $content;
        $subscriptionList['discount'] = $this->getDiscountInfo($element);

        if (false !== stripos($content, 'per month')) {
            $subscriptionList['annualPrice'] = (float) $numericPrice * 12;
        } else {
            $subscriptionList['annualPrice'] = (float) $numericPrice;
        }

        return $subscriptionList;
    }

    /**
     * Check for discount information if present.
     */
    public function getDiscountInfo(Crawler $element): string
    {
        $discount = $element->filter('p');
        if ($discount->count()) {
            return $discount->text();
        }

        return '';
    }

    /**
     * Make the result object keys user friendly.
     */
    public function getKey(string $text): string
    {
        return match ($text) {
            'package-name' => 'name',
            'package-description' => 'description',
            'package-price' => 'price',
            'package-data' => 'terms',
            default => $text,
        };
    }
}
