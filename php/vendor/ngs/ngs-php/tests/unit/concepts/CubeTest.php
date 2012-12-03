<?php
use NGS\Client\StandardProxy;
use Test\Foo;
use Test\FooGrid;
use Test\FooCube;

class CubeTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $items =  Foo::findAll();
        $proxy = new StandardProxy();
        $proxy->delete($items); 
    }

    public function testCube()
    {
        $items = array(
            new Foo(array('bar'=>'a', 'num'=>1)),
            new Foo(array('bar'=>'b', 'num'=>5)),
            new Foo(array('bar'=>'c', 'num'=>null)),
        );
        $proxy = new StandardProxy();
        $proxy->insert($items);

        //$dimensions = array('bar');
        $dimensions = array();
        $facts = array('count', 'total', 'average');
        $olapData = $proxy->olapCube('Test.FooCube', $dimensions, $facts);

        $this->assertSame(2, $olapData[0]->count);
        $this->assertSame(6, $olapData[0]->total);
        $this->assertSame((float)3, $olapData[0]->average);
    }

    public function testCubeWithSpecification()
    {
        $items = array(
            new Foo(array('bar'=>'c', 'num'=>1)),
            new Foo(array('bar'=>'a1', 'num'=>5)),
            new Foo(array('bar'=>'a2', 'num'=>null)),
        );
        $proxy = new StandardProxy();
        $proxy->insert($items);

        $dimensions = array();
        $facts = array('count', 'total', 'average');
        $spec = new FooCube\findByBar(array('query'=>'a'));
        $olapData = $proxy->olapCubeWithSpecification($spec, $dimensions, $facts);

        $this->assertSame(1, $olapData[0]->count);
        $this->assertSame(5, $olapData[0]->total);
        $this->assertSame((float)5, $olapData[0]->average);
    }
}
