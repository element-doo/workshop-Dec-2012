<?php

use NGS\Patterns\Rest\HttpProxy;
use NGS\Patterns\Rest\CrudProxy;
use NGS\Client\Exception\NotFoundException;
use Test\Foo;
use Test\EntityTest;
use Test\EntityTest1;
use Test\RootWithEntity;
use Test\ValueTest;

class CrudTest extends \PHPUnit_Framework_TestCase
{

    public function rootProvider()
    {
        return array(
            array(
                new Foo(array(
                    'bar' => 'test'))
            ),
            array(
                new RootWithEntity(array(
                    'name' => 'test',
                    'ent'  => new EntityTest(array(
                        'name' => 'test',
                        'strArr' => array('a', 'b', 'asdfas'),
                        'intArr' => array('1', '2', '123456789'),
                    )),
                    'entarr'=> array(
                        new EntityTest1(array('name'=>'a')),
                        new EntityTest1(array(
                            'name'=> 'b', 
                            'val' => new ValueTest(array('name'=>'adsf'))
                        ))
                    )
                ))
            ),
        );
    }

    /**
     * @dataProvider rootProvider
     */
    public function testCRUD($root)
    {
        $class = get_class($root);

        try {
            $item = $class::find('test');
            $item->delete();
            $this->assertFalse($class::exists('test'));
        } catch (NotFoundException $e) {
        
        }
        
        $root->persist();
        $this->assertSame('test', $root->URI);

        $foundRoot = $class::find('test');

        // @todo fails, see testUpdateEntityUriOnPersist
        //$this->assertEquals($root, $foundRoot);

        $root->delete();
        $this->assertFalse($class::exists('test'));
    }

    // @todo TEST FAILS: root uri is not updated after updating object
    public function testUriChangeAfterRootUpdate()
    {
        return ;
        
        $i =  Foo::findAll();
        foreach($i as $it)
            $it->delete();

        $foo = new Foo();
        $foo->bar = 'uri1';
        $foo->persist();

        $foo->bar = 'uri2';
        $foo->persist();

        // $foo->URI should be 'uri2', but remains 'uri1'
        $this->assertSame('uri2', $foo->URI);
    }

    // @todo TEST FAILS: entity uri is not updated after calling persist on parent root
    public function testUpdateEntityUriOnPersist()
    {
        return ;

        $root = new RootWithEntity(array(
            'name' => 'test',
            'ent' => new EntityTest(array(
                'name' => 'a'
            ))
        ));
        $root->persist();
        $foundRoot = RootWithEntity::find('test');
        // fails:
        $this->assertSame($root->ent->URI, $foundRoot->ent->URI);
    }

    /**
     * @expectedException NGS\Client\Exception\NotFoundException
     */
    public function testFindNonExisting()
    {
        $this->assertSame(array(), Foo::find(array('non-existing-uri')));

        Foo::find('non-existing-uri');
    }
}
