<?php

namespace Tests\Units;

class QueryParser extends \atoum
{
    public function testCreation()
    {
        $parser = new \QueryParser();

        $this->object($parser)
            ->isInstanceOf('\QueryParser');
    }

    public function testNull()
    {
        $expected = [
            'keywords' => [],
            'tags' => [],
            'fields' => [],
        ];

        $parser = new \QueryParser();

        $tokens = $parser->parse(null);
        $this->array($tokens)
            ->isIdenticalTo($expected);
    }

    public function testEmpty()
    {
        $expected = [
            'keywords' => [],
            'tags' => [],
            'fields' => [],
        ];

        $parser = new \QueryParser();

        $tokens = $parser->parse('');
        $this->array($tokens)
            ->isIdenticalTo($expected);
    }

    public function testSimple()
    {
        $expected = [
            'keywords' => [
                'query',
            ],
            'tags' => [],
            'fields' => [],
        ];

        $parser = new \QueryParser();

        $tokens = $parser->parse('query');
        $this->array($tokens)
            ->isIdenticalTo($expected);
    }

    public function testMultiple()
    {
        $expected = [
            'keywords' => [
                'query',
                'search',
            ],
            'tags' => [],
            'fields' => [],
        ];

        $parser = new \QueryParser();

        $tokens = $parser->parse('query search');
        $this->array($tokens)
            ->isIdenticalTo($expected);
    }

    public function testExact()
    {
        $expected = [
            'keywords' => [
                'query search',
            ],
            'tags' => [],
            'fields' => [],
        ];

        $parser = new \QueryParser();

        $tokens = $parser->parse('"query search"');
        $this->array($tokens)
            ->isIdenticalTo($expected);
    }

    public function testTags()
    {
        $expected = [
            'keywords' => [],
            'tags' => [
                'php',
            ],
            'fields' => [],
        ];

        $parser = new \QueryParser();

        $tokens = $parser->parse('[php]');
        $this->array($tokens)
            ->isIdenticalTo($expected);
    }

    public function testFields()
    {
        $expected = [
            'keywords' => [],
            'tags' => [],
            'fields' => [
                'title' => ['debug'],
            ],
        ];

        $parser = new \QueryParser();

        $tokens = $parser->parse('title:debug');
        $this->array($tokens)
            ->isIdenticalTo($expected);
    }

    public function testAll()
    {
        $expected = [
            'keywords' => [
                'search query',
                'debug',
                'phpinfo',
            ],
            'tags' => [
                'php',
                'xdebug 2',
            ],
            'fields' => [
                'title' => ['php debug'],
                'filename' => ['test.php', 'bootstrap.php'],
            ],
        ];

        $parser = new \QueryParser();

        $tokens = $parser->parse('"search query" debug phpinfo [php] [xdebug 2] "title:php debug" filename:test.php filename:bootstrap.php');
        $this->array($tokens)
            ->isIdenticalTo($expected);
    }
}
