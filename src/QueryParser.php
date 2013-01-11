<?php

class QueryParser
{
    public function parse($query)
    {
        $tokens = array(
            'keywords' => array(),
            'tags' => array(),
            'fields' => array(),
        );

        $regex = '["|\[].*?["|\]]|[^\s]+';
        preg_match_all("#$regex#", $query, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $match = array_pop($match);
            $match = trim($match, '"');

            if ($match{0} === '[') {
                $tokens['tags'][] = trim($match, '[]');
            }
            elseif (strpos($match, ':') !== false) {
                list($name, $value) = explode(':', $match, 2);
                $tokens['fields'][$name][] = $value;
            }
            else {
                $tokens['keywords'][] = $match;
            }
        }
        return $tokens;
    }
}
