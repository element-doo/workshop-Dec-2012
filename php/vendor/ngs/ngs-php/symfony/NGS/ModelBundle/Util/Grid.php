<?php
namespace NGS\ModelBundle\Util;

use Symfony\Component\HttpFoundation\Request;
use NGS\Patterns\Specification;

abstract class Grid
{
    // Helper for generating grid with paginator/search from specification
    public static function fromSpecification(Specification $specification, Request $request, $forceSearch = false)
    {
        if($forceSearch === false && count($request->query) == 0)
            return array(
                'search'    => $specification->toArray(),
                'items'     => array()
            );

        $paginator = new Paginator(
            null,
            $request->get('page'),
            $request->get('items'));

        $items = $specification->search($paginator->getPerPage(), $paginator->getStart());

        $paginator->setCount($specification->count());

        return array(
            'search'    => $specification->toArray(),
            'paginator' => $paginator,
            'items'     => $items
        );
    }
}
