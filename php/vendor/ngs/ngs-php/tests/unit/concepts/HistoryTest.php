<?php
use Test\Foo;
use NGS\Client\StandardProxy;

class HistoryTest extends \BaseTestCase
{
    public function setUp()
    {
        $this->deleteAll('Test\Foo');
    }

    public function testHistory()
    {
        $foo =  new Foo();
        
        $foo->bar = (string)\NGS\UUID::v4();
        $foo->persist();

        $newFoo = clone $foo;
        $newFoo->num = 5;
        $newFoo->persist();

        $newFoo->delete();

        $history = Foo::getHistory($foo->URI);
        
        $this->assertEquals('INSERT', $history[0]->action);
        $this->assertEquals($foo, $history[0]->value);

        $this->assertEquals('UPDATE', $history[1]->action);
        $this->assertEquals($newFoo, $history[1]->value);

        $this->assertEquals('DELETE', $history[2]->action);
        $this->assertEquals($newFoo, $history[2]->value);
    }
}
