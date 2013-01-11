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

    public function search($query, $length, $page)
    {
        $parser = new \QueryParser();
        $tokens = $parser->parse($query);

        list($where, $params) = $this->tokensToWhereClause($tokens);

        return $this->paginateFindWhere(
            $where, $params, 'ORDER BY created DESC', $length, $page
        );
    }

    private function tokensToWhereClause($tokens)
    {
        $where = '1 = 1';
        $params = array();

        foreach ($tokens['keywords'] as $keyword) {
            $where .= ' AND (title ILIKE ? OR code ILIKE ?)';
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
        }

        if (!empty($tokens['tags'])) {
            $where .= ' AND keywords @> ?';
            $params[] = '{' . implode(', ', $tokens['tags']) . '}';
        }

        $fields = $this->getFields();
        foreach ($tokens['fields'] as $name => $values) {
            if (isset($fields[$name])) {
                foreach ($values as $value) {
                    $where .= ' AND LOWER(' . $name . ') = LOWER(?)';
                    $params[] = $value;
                }
            }
            else {
                throw new \RuntimeException("Unknom field: $name");
            }
        }

        return array($where, $params);
    }
}
