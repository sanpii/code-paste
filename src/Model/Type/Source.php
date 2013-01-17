<?php

namespace Model\Type;

class Source
{
    public $name;
    public $content;
    public $language;

    public function __construct($name, $content, $language)
    {
        $this->name = $name;
        $this->content = $content;
        $this->language = $language;
    }
}
