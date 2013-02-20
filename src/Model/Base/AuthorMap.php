<?php

namespace Model\Base;

use \Pomm\Object\BaseObjectMap;
use \Pomm\Exception\Exception;

abstract class AuthorMap extends BaseObjectMap
{
    public function initialize()
    {

        $this->object_class =  'Model\Author';
        $this->object_name  =  'public.author';

        $this->addField('id', 'int4');
        $this->addField('name', 'varchar');
        $this->addField('password', 'varchar');

        $this->pk_fields = array('id');
    }
}