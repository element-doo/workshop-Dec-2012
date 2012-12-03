<?php
use Store\ProductList;
use Store\Product;
use Store\Group;

class DetailTest extends \BaseTestCase
{
    public function setUp()
    {
        $this->deleteAll('Store\Product');
        $this->deleteAll('Store\Group');
    }

    public function testDetail()
    {
        $group = new Group(array('Name'=>'test'));
        $group->persist();

        $prod = new Product(array(
            'Name'  =>'second',
            'Price' =>'100',
            'Group' => $group
        ));
        $prod->persist();

        $item = Group::find($group->URI);

        $this->assertEquals($prod->URI, $item->Products[0]->URI);
    }

    /**
     * @expectedException \LogicException
     */
    public function testReadOnly()
    {
        $group = new Group(array('Name'=>'test'));
        $group->Products = null;
    }
    
}
