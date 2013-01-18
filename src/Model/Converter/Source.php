<?php

namespace Model\Converter;

use Pomm\Converter\PgString;
use Pomm\Exception\Exception;
use Pomm\Converter\ConverterInterface;

class Source implements ConverterInterface
{
    protected $className;
    protected $stringConverter;

    public function __construct(
        $className = 'Model\Type\Source',
        PgString $stringConverter = null
    ) {
        $this->className = $className;
        $this->stringConverter = is_null($stringConverter) ? new PgString() : $stringConverter;
    }

    public function fromPg($data, $type = null)
    {
        $data = trim($data, "()");
        $values = str_getcsv($data, ',');

        return new $this->className(
            str_replace('\\\\', '\\', $this->stringConverter->fromPg($values[0])),
            str_replace('\\\\', '\\', $this->stringConverter->fromPg($values[1])),
            str_replace('\\\\', '\\', $this->stringConverter->fromPg($values[2]))
        );
    }

    public function toPg($data, $type = null)
    {
        return sprintf(
            "(%s, %s, %s)",
            $this->stringConverter->toPg($data['name']),
            $this->stringConverter->toPg($data['content']),
            $this->stringConverter->toPg($data['language'])
        );
    }
}
