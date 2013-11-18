<?php

namespace Model;

class Database extends \Pomm\Connection\Database
{
    protected function initialize()
    {
        parent::initialize();

        $converter = new \Pomm\Converter\PgRow(
            $this,
            new \Pomm\Object\RowStructure([
                'name' => 'varchar',
                'content' => 'text',
                'language' => 'varchar',
            ]),
            '\Model\Type\Source'
        );

        $this->registerConverter('Source', $converter, ['public.source', 'source']);
    }
}
