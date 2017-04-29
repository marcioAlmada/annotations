<?php

namespace Minime\Annotations\Types;

use Minime\Annotations\Interfaces\TypeInterface;

abstract class AbstractType implements TypeInterface
{
    /**
     * @return TypeInterface
     */
    abstract public static function getType();
}
