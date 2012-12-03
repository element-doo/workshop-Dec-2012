<?php

namespace NGS\ModelBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ModelBundle extends Bundle
{
    /**
     * Inits NGS REST repository
     * Uses extended SF2 Repository that dispatches request/response event for easy logging
     */
    public function boot()
    {
    }
}
