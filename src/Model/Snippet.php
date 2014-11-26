<?php

namespace Model;

use \PommProject\Foundation\Where;
use \PommProject\ModelManager\Model\FlexibleEntity;

class Snippet extends Base\SnippetModel
{
    public function search($query, $length, $page)
    {
        $parser = new \QueryParser();
        $tokens = $parser->parse($query);

        $where = $this->tokensToWhere($tokens);

        return $this->paginateFindWhere($where, $length, $page);
    }

    private function tokensToWhere($tokens)
    {
        $where = new Where();

        foreach ($tokens['keywords'] as $keyword) {
            $where->andWhere(
                '(title ILIKE $* OR (unnest_codes.code).name ILIKE $* OR (unnest_codes.code).content ILIKE $*)',
                array_fill(0, 3, "%$keyword%")
            );
        }

        if (!empty($tokens['tags'])) {
            $where->andWhere(
                'keywords @> $*',
                ['{' . implode(', ', $tokens['tags']) . '}']
            );
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
                $where->andWhere(
                    'LOWER(' . $field . ') = LOWER($*)',
                    [$value]
                );
            }
        }

        return $where;
    }
}
