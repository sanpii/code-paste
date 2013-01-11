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
        $expected = array(
            'keywords' => array(),
            'tags' => array(),
            'fields' => array(),
        );

        $parser = new \QueryParser();

        $tokens = $parser->parse(null);
        $this->array($tokens)
            ->isIdenticalTo($expected);
    }

    public function testEmpty()
    {
        $expected = array(
            'keywords' => array(),
            'tags' => array(),
            'fields' => array(),
        );

        $parser = new \QueryParser();

        $tokens = $parser->parse('');
        $this->array($tokens)
            ->isIdenticalTo($expected);
    }

    public function testSimple()
    {
        $expected = array(
            'keywords' => array(
                'query',
            ),
            'tags' => array(),
            'fields' => array(),
        );

        $parser = new \QueryParser();

        $tokens = $parser->parse('query');
        $this->array($tokens)
            ->isIdenticalTo($expected);
    }

    public function testMultiple()
    {
        $expected = array(
            'keywords' => array(
                'query',
                'search',
            ),
            'tags' => array(),
            'fields' => array(),
        );

        $parser = new \QueryParser();

        $tokens = $parser->parse('query search');
        $this->array($tokens)
            ->isIdenticalTo($expected);
    }

    public function testExact()
    {
        $expected = array(
            'keywords' => array(
                'query search',
            ),
            'tags' => array(),
            'fields' => array(),
        );

        $parser = new \QueryParser();

        $tokens = $parser->parse('"query search"');
        $this->array($tokens)
            ->isIdenticalTo($expected);
    }

    public function testTags()
    {
        $expected = array(
            'keywords' => array(),
            'tags' => array(
                'php',
            ),
            'fields' => array(),
        );

        $parser = new \QueryParser();

        $tokens = $parser->parse('[php]');
        $this->array($tokens)
            ->isIdenticalTo($expected);
    }

    public function testFields()
    {
        $expected = array(
            'keywords' => array(),
            'tags' => array(),
            'fields' => array(
                'title' => array('debug'),
            ),
        );

        $parser = new \QueryParser();

        $tokens = $parser->parse('title:debug');
        $this->array($tokens)
            ->isIdenticalTo($expected);
    }

    public function testAll()
    {
        $expected = array(
            'keywords' => array(
                'search query',
                'debug',
                'phpinfo',
            ),
            'tags' => array(
                'php',
                'xdebug 2',
            ),
            'fields' => array(
                'title' => array('php debug'),
                'filename' => array('test.php', 'bootstrap.php'),
            ),
        );

        $parser = new \QueryParser();

        $tokens = $parser->parse('"search query" debug phpinfo [php] [xdebug 2] "title:php debug" filename:test.php filename:bootstrap.php');
        $this->array($tokens)
            ->isIdenticalTo($expected);
    }
}
