<?php
use Scraping\Scrape;

class OptimisticConcurrencyTest extends \BaseTestCase
{
    public function setUp()
    {
        $this->deleteAll('Scraping\Scrape');
    }

    // @todo not implemented
    public function testOptimisticConcurrency()
    {
        $scrape = new Scrape();
//        $scrape->persist();

//        $scrape->persist();

    }
}
