<?php
use Billing\Invoice;
use Billing\Item;

class CalculatedExpressionTest extends \BaseTestCase
{
    public function setUp()
    {
        $this->deleteAll('Billing\Invoice');
    }

    public function testCalculatedExpression()
    {
        $invoice = new Invoice(array(
            'Items' => array(
                new Item(array('Product' => 'a', 'Price' => '10')),
                new Item(array('Product' => 'b', 'Price' => '20')),
                new Item(array('Product' => 'c', 'Price' => '30')),
            )
        ));
        $invoice->persist();

        $this->assertEquals(new \NGS\Money(60), $invoice->Total);
    }
}
