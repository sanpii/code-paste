<?php

namespace Model\Type\Stucture;

use \PommProject\ModelManager\Model\RowStructure;

class Source extends RowStructure
{
    public function __construct()
    {
        $this->addField('name', 'varchar')
            ->addField('content', 'varchar')
            ->addField('language', 'varchar');
    }
}
