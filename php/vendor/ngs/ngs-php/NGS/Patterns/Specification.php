<?php
namespace NGS\Patterns;

require_once(__DIR__.'/IDomainObject.php');
require_once(__DIR__.'/../Client/DomainProxy.php');

use NGS\Client\DomainProxy;

abstract class Specification implements IDomainObject
{
    public function search($limit = null, $offset = null)
    {
        return DomainProxy::instance()->searchWithSpecification($this, $limit, $offset);
    }

    public function count()
    {
        return DomainProxy::instance()->countWithSpecification($this);
    }
}
