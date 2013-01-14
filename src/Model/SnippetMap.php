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
            $where .= ' AND ((code).name ILIKE ? OR (code).content ILIKE ?)';
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
        }

        if (!empty($tokens['tags'])) {
            $where .= ' AND keywords @> ?';
            $params[] = '{' . implode(', ', $tokens['tags']) . '}';
        }

        foreach ($tokens['fields'] as $name => $values) {
            switch($name) {
                case 'title':
                    $field = '(code).name';
                break;
                case 'code':
                    $field = '(code).content';
                break;
                default:
                    throw new \RuntimeException("Unknom field: $name");
                break;
            }

            foreach ($values as $value) {
                $where .= ' AND LOWER(' . $field . ') = LOWER(?)';
                $params[] = $value;
            }
        }

        return array($where, $params);
    }
}
