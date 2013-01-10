<?php

namespace Model;

use Model\Base\SnippetMap as BaseSnippetMap;
use Model\Snippet;
use \Pomm\Query\Where;
use \Pomm\Object\BaseObject;

class SnippetMap extends BaseSnippetMap
{
    public function saveOne(BaseObject &$object)
    {
        $object->keywords = array_filter($object->keywords, function($keyword) {
            return !empty($keyword);
        });

        parent::saveOne($object);
    }
}
