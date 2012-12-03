<?php
use Test\Foo;
use NGS\Client\Exception\NotFoundException;

class SpecificationTest extends \PHPUnit_Framework_TestCase
{
    protected $items = array();

    protected function setUp()
    {
        $items = array(
            new Foo(array('bar'=>'abcd_test')),
            new Foo(array('bar'=>'abcd_test2')),
            new Foo(array('bar'=>'blablatost'))
        );
        foreach($items as $item) {
            try {
                $item = Foo::find($item->bar);
            }
            catch (NotFoundException $e) {
                $item->persist();
            }
            $this->items[] = $item;
        }
    }

    protected function tearDown()
    {
        foreach($this->items as $item)
            $item->delete();
    }

    public function testSearch()
    {
        $items = Foo::searchByBar('abcd');

        $this->assertSame(2, count($items));
        
        $limit = 1;
        $offset = 1;
        $items = Foo::searchByBar('abcd', $limit, $offset);

        $this->assertEquals(array($this->items[1]), $items);        
    }
}
