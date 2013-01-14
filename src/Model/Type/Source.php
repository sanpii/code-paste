<?php

namespace Model\Type;

class Source
{
    public $name;
    public $content;

    public function __construct($name, $content)
    {
        $this->name = $name;
        $this->content = $content;
    }
}
