<?php

namespace Minime\Annotations\Fixtures;

use Minime\Annotations\Interfaces\TypeInterface;

class FooType implements TypeInterface
{
    public static function getType()
    {
        return new FooType();
    }

    public function parse($value, $annotation = null)
    {
        return 'this foo is ' . $value;
    }
}
