<?php
namespace NGS\Patterns;

require_once(__DIR__.'/Identifiable.php');
require_once(__DIR__.'/../Client/DomainProxy.php');

use NGS\Client\DomainProxy;

abstract class DomainEvent extends Identifiable
{
    public function submit()
    {
        return DomainProxy::instance()->submit($this);
    }
}
