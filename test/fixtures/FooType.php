<?php

namespace Minime\Annotations\Fixtures;

use Minime\Annotations\Interfaces\TypeInterface;

class FooType implements TypeInterface
{
    public function parse($value, $annotation = null)
    {
        return 'this foo is ' . $value;
    }
}
