<?php

namespace Model;

use \Model\Type\Stucture\Source;
use \PommProject\Foundation\Session\Session;
use \PommProject\ModelManager\Converter\PgEntity;
use \PommProject\ModelManager\SessionBuilder as BaseSessionBuilder;

class SessionBuilder extends BaseSessionBuilder
{
    protected function postConfigure(Session $session)
    {
        parent::postConfigure($session);

        $converter_holder = $session->getPoolerForType('converter')
            ->getConverterHolder();

        $converter_holder->registerConverter(
            'Source',
            new PgEntity('\Model\Type\Source', new Source),
            ['public.source', 'source']);
    }
}
