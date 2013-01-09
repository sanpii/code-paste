<?php

namespace Model\Base;

use \Pomm\Object\BaseObjectMap;
use \Pomm\Exception\Exception;

abstract class SnippetMap extends BaseObjectMap
{
    public function initialize()
    {

        $this->object_class =  'Model\Snippet';
        $this->object_name  =  'public.snippet';

        $this->addField('id', 'int4');
        $this->addField('keywords', 'varchar[]');
        $this->addField('language', 'varchar');
        $this->addField('title', 'varchar');
        $this->addField('code', 'varchar');
        $this->addField('created', 'timestamp');
        $this->addField('updated', 'timestamp');

        $this->pk_fields = array('id');
    }
}
