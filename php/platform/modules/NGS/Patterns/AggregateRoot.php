<?php
namespace NGS\Patterns;

require_once(__DIR__.'/Identifiable.php');
require_once(__DIR__.'/../Client/CrudProxy.php');
require_once(__DIR__.'/../Client/DomainProxy.php');

use NGS\Client\DomainProxy;
use NGS\Client\CrudProxy;
use NGS\Patterns\Identifiable;

abstract class AggregateRoot extends Identifiable
{
    public static function findAll($limit = null, $offset = null)
    {
        return DomainProxy::instance()->search(get_called_class(), $limit, $offset);
    }

    public static function count(Specification $specification = null)
    {
        return $specification === null
            ? DomainProxy::instance()->count(get_called_class())
            : $specification->count();
    }

    /**
     * Inserts or updates object
     * @param object instance of object
     */
    public function persist()
    {
        return $this->getURI() === null
            ? CrudProxy::instance()->create($this)
            : CrudProxy::instance()->update($this);
    }

    public function delete()
    {
        return CrudProxy::instance()->delete(get_called_class(), $this->getURI());
    }
}
