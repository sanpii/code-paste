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

        $object->codes = array_filter($object->codes, function($code) {
            return !empty($code['content']);
        });

        parent::saveOne($object);
    }

    public function search($query, $length, $page)
    {
        $parser = new \QueryParser();
        $tokens = $parser->parse($query);

        list($where, $params) = $this->tokensToWhereClause($tokens);

        $sql = <<<EOD
WITH
unnest_codes (id, code) AS (SELECT id, unnest(codes) AS code FROM snippet)
SELECT DISTINCT snippet.* FROM snippet NATURAL JOIN unnest_codes WHERE $where ORDER BY created DESC
EOD;
        $sqlCount = <<<EOD
WITH
unnest_codes (id, code) AS (SELECT id, unnest(codes) AS code FROM snippet)
SELECT DISTINCT COUNT(*) FROM snippet NATURAL JOIN unnest_codes WHERE $where
EOD;

        return $this->paginateQuery($sql, $sqlCount, $params, $length, $page);
    }

    private function tokensToWhereClause($tokens)
    {
        $where = '1 = 1';
        $params = array();

        foreach ($tokens['keywords'] as $keyword) {
            $where .= ' AND (title ILIKE ? OR (unnest_codes.code).name ILIKE ? OR (unnest_codes.code).content ILIKE ?)';
            $params[] = "%$keyword%";
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
                    $field = '(unnest_codes.code).name';
                break;
                case 'code':
                    $field = '(unnest_codes.code).content';
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
